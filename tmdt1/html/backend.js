/* PHP/MySQL bridge. HTML/CSS gốc được giữ nguyên. */
(function(){
 const root='../api/index.php?action=';
 async function call(action,data){const r=await fetch(root+action,{method:data?'POST':'GET',headers:{'Content-Type':'application/json'},credentials:'same-origin',body:data?JSON.stringify(data):undefined});let j=await r.json();if(!r.ok)throw Error(j.error||'Lỗi máy chủ');return j;}
 window.StarbucksAPI=call;
 document.addEventListener('DOMContentLoaded',()=>{
  const registration=document.getElementById('registerForm');
  if(registration)registration.addEventListener('submit',async e=>{
   e.preventDefault(); e.stopImmediatePropagation();
   const value=id=>(document.getElementById(id)?.value||'').trim();
   const msg=registration.querySelector('.message')||document.createElement('div');if(!msg.parentNode)registration.append(msg);
   if(value('password')!==value('confirmPassword')){alert('Mật khẩu xác nhận không khớp');return;}
   try{await call('register',{fullname:value('fullname'),email:value('email'),phone:value('phone'),password:value('password')});alert('Đăng ký thành công. Hãy đăng nhập.');location.href='dangnhap.html';}
   catch(err){alert(err.message)}
  },true);
  const login=document.getElementById('loginForm');
  if(login)login.addEventListener('submit',async e=>{
   e.preventDefault();e.stopImmediatePropagation();
   const username=document.getElementById('loginName')?.value.trim();
   const password=document.getElementById('loginPassword')?.value || login.querySelector('input[type="password"]')?.value || '';
   try{const res=await call('login',{login:username,password});localStorage.setItem('currentUser',JSON.stringify(res.user));localStorage.removeItem('users');location.href='index.html';}
   catch(err){alert(err.message)}
  },true);
  // Display logged-in user from the real PHP session (not from client-controlled localStorage).
  call('me').then(({user})=>{if(user)localStorage.setItem('currentUser',JSON.stringify(user));else localStorage.removeItem('currentUser');}).catch(()=>{});
 });
})();
