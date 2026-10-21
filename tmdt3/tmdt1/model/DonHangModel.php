<?php
/** MODEL: tạo đơn hàng và chi tiết đơn hàng trong một transaction. */
function taoDonHang(mysqli $conn, array $items, string $name, string $phone, string $address, string $method, ?int $userId): array {
    if (!in_array($method, ['cod', 'bank_transfer'], true) || mb_strlen($name) < 2 || mb_strlen($address) < 5) {
        throw new DomainException('Cần nhập tên và địa chỉ giao hàng hợp lệ');
    }
    if (!$items || !is_array($items) || count($items) > 100) {
        throw new DomainException('Giỏ hàng trống hoặc không hợp lệ');
    }

    $conn->begin_transaction();
    try {
        $total = 0;
        $validated = [];
        foreach ($items as $item) {
            $productId = (int)($item['id'] ?? 0);
            $quantity = (int)($item['quantity'] ?? 0);
            if ($productId < 1 || $quantity < 1 || $quantity > 99) {
                throw new DomainException('Số lượng hoặc ID sản phẩm không hợp lệ');
            }
            $product = laySanPhamDeDatHang($conn, $productId);
            if (!$product || (int)$product['stock'] < $quantity) {
                throw new DomainException('Sản phẩm không tồn tại hoặc không đủ tồn kho: ' . $productId);
            }
            $total += (float)$product['price'] * $quantity;
            $validated[] = [$product, $quantity];
        }

        $code = 'SB' . strtoupper(bin2hex(random_bytes(5)));
        $insert = $conn->prepare('INSERT INTO orders(code,user_id,customer_name,phone,address,total,payment_method) VALUES(?,?,?,?,?,?,?)');
        $insert->bind_param('sisssds', $code, $userId, $name, $phone, $address, $total, $method);
        $insert->execute();
        $orderId = $conn->insert_id;

        $insertItem = $conn->prepare('INSERT INTO order_items(order_id,product_id,product_name,price,quantity) VALUES(?,?,?,?,?)');
        $updateStock = $conn->prepare('UPDATE products SET stock=stock-? WHERE id=?');
        foreach ($validated as [$product, $quantity]) {
            $productId = (int)$product['id'];
            $productName = $product['name'];
            $price = (float)$product['price'];
            $insertItem->bind_param('iisdi', $orderId, $productId, $productName, $price, $quantity);
            $insertItem->execute();
            $updateStock->bind_param('ii', $quantity, $productId);
            $updateStock->execute();
        }
        $conn->commit();
        return ['id' => $orderId, 'code' => $code, 'total' => $total];
    } catch (Throwable $e) {
        $conn->rollback();
        throw $e;
    }
}
