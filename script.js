const headerImage = document.querySelector('.header-image');
const imageSquares = document.querySelectorAll('.image-square');
const total = document.querySelector('.total');

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
