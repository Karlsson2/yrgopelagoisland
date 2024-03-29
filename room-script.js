const bookingForm = document.querySelector('.booking-form');
const dates = document.querySelector('input[name="datefilter"');
const roomPrice = document.getElementById('pricePerNight');
const totalValue = document.querySelector('.total');
const subTotal = document.querySelector('.subtotal');
const discountTotal = document.querySelector('.discountTotal');
const copyButton = document.querySelector('#copyButton');
const hiddenCopyMessage = document.querySelector('.copy-message');
const codeBefore = document.querySelector('.code-before');

//copy the contents of the code area to the clipboard
async function copyToClipboard() {
  const codeElement = document.getElementById('codeElement');
  try {
    await navigator.clipboard.writeText(codeElement.textContent);
  } catch (err) {}
}
if (copyButton !== null) {
  copyButton.addEventListener('click', function () {
    copyToClipboard();
    hiddenCopyMessage.classList.add('fadeInOut');
    codeBefore.classList.add('fadeInOutOpacity');
    setTimeout(function () {
      hiddenCopyMessage.classList.remove('fadeInOut');
      codeBefore.classList.remove('fadeInOutOpacity');
    }, 3000);
  });
}

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
//get the total price for the room.
function getRoomTotalPrice() {
  if (dates.value == null || dates.value == '') {
    return 0;
  } else {
    const days = getDays();
    const totalRoomPrice = days * roomPrice.value;
    return totalRoomPrice;
  }
}
//get the total days
function getDays() {
  let daysFieldValue = dates.value;

  daysFieldValue = daysFieldValue.trim();

  // Check if the value is empty
  if (daysFieldValue === '') {
    return 0;
  } else {
    const dateArray = dates.value.split(' - ');
    const days = calculateDays(dateArray[0], dateArray[1]);
    return days;
  }
}
//get the total price
function getTotalPrice() {
  const roomPrice = getRoomTotalPrice();
  const activitiesPrices = getActivitiesPrice();
  const totalPrice = roomPrice + activitiesPrices;
  return totalPrice;
}
//get the price for the activities
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
function getDiscounts() {
  const discountContainers = document.querySelectorAll('.discount-description');
  const discountArray = [];

  discountContainers.forEach((container) => {
    // Get the values of data attributes
    const percentage = parseFloat(container.dataset.percentage);
    const days = parseInt(container.dataset.days);

    // Check if both data attributes are present
    if (!isNaN(percentage) && !isNaN(days)) {
      // Add an object { percentage, days } to the discount array
      discountArray.push({ percentage, days });
    }
  });

  return discountArray;
}

function checkForDiscounts() {
  const days = getDays();
  const discounts = getDiscounts();
  let bestDiscount = 0;

  discounts.forEach((discount) => {
    if (days >= discount.days && discount.percentage >= bestDiscount) {
      bestDiscount = discount.percentage;
    }
  });
  return bestDiscount;
}
if (bookingForm !== null) {
  bookingForm.addEventListener('change', function () {
    const totalPrice = getTotalPrice();
    const discounts = checkForDiscounts();

    calculateDiscount(totalPrice, discounts);
  });
}
$('input[name="datefilter"]').on('change', function () {
  const totalPrice = getTotalPrice();
  const discounts = checkForDiscounts();
  calculateDiscount(totalPrice, discounts);
});

function calculateDiscount(totalPrice, discounts) {
  if (discounts > 0) {
    const discountedPrice = totalPrice - totalPrice * discounts;
    const discountTotalPrice = (totalPrice * discounts).toPrecision(2);
    totalValue.textContent = 'Total: $' + discountedPrice.toPrecision(3);
    subTotal.textContent = 'Subtotal: $' + totalPrice.toPrecision(3);
    discountTotal.textContent =
      'Discount: -$' +
      discountTotalPrice +
      '(' +
      Math.floor(discounts * 100) +
      '%)';
  } else {
    subTotal.textContent = 'Subtotal: $' + totalPrice.toPrecision(3);
    discountTotal.textContent = '';
    totalValue.textContent = 'Total: $' + totalPrice.toPrecision(3);
  }
}
