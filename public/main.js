const canvas = document.querySelector('canvas');
const context = canvas.getContext('2d');

let data, stops, dimensions;

function addYearFilters(data) {
  const yearFiltersWrap = document.querySelector('.filter-years');
  const years = Object.keys(data).sort().reverse();
  const latestYear = Object.keys(data).sort().pop();

  years.forEach((year) => {
    let radioButton = document.createElement('input');

    radioButton.type = 'radio';
    radioButton.name = 'year';
    radioButton.value = year;
    radioButton.id = `filter-year-${year}`;

    if (year === latestYear) {
      radioButton.checked = true;
    }

    radioButton.addEventListener('click', () => {
      stops = data[year].stops;
      dimensions = data[year].dimensions;

      drawStops(stops, dimensions);
    });

    let radioButtonLabel = document.createElement('label');

    radioButtonLabel.htmlFor = `filter-year-${year}`;
    radioButtonLabel.textContent = year;

    yearFiltersWrap.appendChild(radioButton);
    yearFiltersWrap.appendChild(radioButtonLabel);
  });
}

async function getData() {
  const data = await fetch('data.php');
  const dataJson = data.json();

  return dataJson;
}

function resizeCanvas() {
  canvas.removeAttribute('width');
  canvas.removeAttribute('height');

  canvas.width = canvas.offsetWidth;
  canvas.height = canvas.offsetHeight;

  context.translate(0, canvas.height);
  context.clearRect(0, 0, canvas.width, canvas.height);
}

function drawStop(x, y, size, color = '#ffffff') {
  context.beginPath();
  context.arc(x * canvas.width, -(y * canvas.height), (20 * size + 1), 0, 2 * Math.PI);
  context.fillStyle = color;
  context.fill();
}

async function drawStops(stops, dimensions) {
  resizeCanvas();

  const lonToX = (lon) => ((lon - dimensions.lon.min) / (dimensions.lon.max - dimensions.lon.min));
  const latToY = (lat) => ((lat - dimensions.lat.min) / (dimensions.lat.max - dimensions.lat.min));
  const frequency = (departures) => ((departures - dimensions.departures.min) / (dimensions.departures.max - dimensions.departures.min));

  stops.forEach((stop) => {
    drawStop(lonToX(stop.lon), latToY(stop.lat), frequency(stop.departures));
  });
}

async function initialize() {
  data = await getData();
  stops = await data[Object.keys(data).sort().pop()].stops;
  dimensions = await data[Object.keys(data).sort().pop()].dimensions;

  addYearFilters(data);

  (new ResizeObserver(() => drawStops(stops, dimensions))).observe(canvas);

  drawStops(stops, dimensions);
}

initialize();
