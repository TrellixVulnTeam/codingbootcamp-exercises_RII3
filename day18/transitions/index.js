const element = document.querySelector("#exercise_fixed_box");

element.addEventListener('click', () => {
    element.classList.toggle('clicked');
})

const box = document.querySelector("#exercise_bg_box");

box.addEventListener('click', () => {
    box.classList.toggle('change');
})