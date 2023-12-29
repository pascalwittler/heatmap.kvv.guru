const canvas = document.querySelector('canvas');
const context = canvas.getContext('2d');

canvas.width = canvas.offsetWidth;
canvas.height = canvas.offsetHeight;

context.translate(0, canvas.height);

async function getData() {
  const data = await fetch('data.php');
  const dataJson = data.json();

  return dataJson;
}

function drawStop(x, y, size, color = '#ffffff') {
  context.beginPath();
  context.arc(x * canvas.width, -(y * canvas.height), (20 * size + 1), 0, 2 * Math.PI);
  context.fillStyle = color;
  context.fill();
}

async function initialize() {
  const data = await getData();
}

initialize();
