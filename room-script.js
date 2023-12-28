const headerImage = document.querySelector('.header-image');
const imageSquares = document.querySelectorAll('.image-square');
const bookingForm = document.querySelector('.booking-form');
const dates = document.querySelector('input[name="datefilter"');
const roomPrice = document.getElementById('pricePerNight');
const totalValue = document.querySelector('.total');

imageSquares.forEach((imageSquare) => {
  imageSquare.addEventListener('click', (event) => {
    const backgroundImage = headerImage.style.backgroundImage;
    const imageSquareImage = imageSquare.src;

    headerImage.style.backgroundImage = `url('${imageSquareImage}')`;
    imageSquare.src = extractImageUrl(backgroundImage);
  });
});

function extractImageUrl(cssUrlString) {
  // Regular expression to extract the URL from the cssUrlString
  const urlRegex = /url\("(.+)"\)/;
  const matches = cssUrlString.match(urlRegex);

  // Check if there is a match and return the URL
  return matches ? matches[1] : null;
}
function calculateDays(startDate, endDate) {
  date1 = new Date(parseEuropeanDate(startDate));
  date2 = new Date(parseEuropeanDate(endDate));
  const time_difference = date2.getTime() - date1.getTime();
  const days_difference = time_difference / (1000 * 60 * 60 * 24);
  return days_difference + 1;
}

function parseEuropeanDate(dateString) {
  const [day, month, year] = dateString.split('/');
  // Note: Months in JavaScript are 0-indexed, so we subtract 1 from the month
  return new Date(year, month - 1, day);
}
function getRoomTotalPrice() {
  if (dates.value == null || dates.value == '') {
    return 0;
  } else {
    const dateArray = dates.value.split(' - ');
    const days = calculateDays(dateArray[0], dateArray[1]);
    const totalRoomPrice = days * roomPrice.value;
    return totalRoomPrice;
  }
}

function getTotalPrice() {
  const roomPrice = getRoomTotalPrice();
  const activitiesPrices = getActivitiesPrice();
  return roomPrice + activitiesPrices;
}

function getActivitiesPrice() {
  const selectedCheckboxes = document.querySelectorAll(
    'input[name="selected_features[]"]:checked'
  );
  const selectedPrices = Array.from(selectedCheckboxes).map(function (
    checkbox
  ) {
    return parseFloat(checkbox.getAttribute('data-price'));
  });
  if (selectedCheckboxes.length < 1) {
    return 0;
  }
  // Calculate the total price by summing up the selected prices
  else {
    const totalPrice = selectedPrices.reduce(function (total, price) {
      return total + price;
    });
    return totalPrice;
  }
}

bookingForm.addEventListener('change', function () {
  const totalPrice = getTotalPrice();
  totalValue.textContent = 'Total Price: $' + totalPrice;
});
$('input[name="datefilter"]').on('change', function () {
  const totalPrice = getTotalPrice();
  totalValue.textContent = 'Total Price: $' + totalPrice;
});
