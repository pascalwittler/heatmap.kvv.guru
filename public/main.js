const canvas = document.querySelector('canvas');
const context = canvas.getContext('2d');

canvas.width = canvas.offsetWidth;
canvas.height = canvas.offsetHeight;

context.translate(0, canvas.height);
