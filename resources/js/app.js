const container =document.querySelector('.container');
const RegisterBtn =document.querySelector('.register-btn');
const LoginBtn =document.querySelector('.login-btn');

LoginBtn.addEventListener('click',() =>{
    container.classList.add('active');
});

RegisterBtn.addEventListener('click',() =>{
    container.classList.remove('active');
});