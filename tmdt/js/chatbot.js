(function(){
 const widget=document.querySelector('.contact-chat');
 const messages=document.getElementById('aiChatMessages');
 const questions=document.getElementById('aiChatSuggestions');
 const reset=document.getElementById('faqChatReset');
 if(!widget||!messages||!questions)return;
 const welcome='Chào bạn, mình có thể giúp gì? Hãy chọn một câu hỏi bên dưới để xem hướng dẫn nhanh nhé. ✨';
 function addMessage(role,text){const row=document.createElement('div');row.className='ai-message '+role;if(role==='assistant'){const mark=document.createElement('span');mark.className='ai-message-mark';mark.textContent='S';row.appendChild(mark);}const bubble=document.createElement('div');bubble.className='ai-bubble';bubble.textContent=text;row.appendChild(bubble);messages.appendChild(row);messages.scrollTop=messages.scrollHeight;}
 function showWelcome(){messages.innerHTML='';addMessage('assistant',welcome);}
 questions.addEventListener('click',function(event){const button=event.target.closest('button[data-question]');if(!button)return;addMessage('user',button.dataset.question||'');const reply=addMessage('assistant',button.dataset.answer||'Bạn vui lòng liên hệ Facebook hoặc Zalo để được hỗ trợ nhé.');reply.classList.add('faq-answer-pop');});
 if(reset)reset.addEventListener('click',showWelcome);
 showWelcome();
})();