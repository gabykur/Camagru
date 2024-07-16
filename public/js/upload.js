const canvas = document.getElementById('canvas');
const canvasCopy = document.getElementById('canvasCopy');
const context = canvas.getContext('2d');
const picture = document.getElementById('upload_img');
const uploadButton = document.getElementById('uploadBtt');
const overlayImage = document.getElementById('overlay');

const stickerSelector = () => {
  const selectedSticker = document.querySelector('#sticker_div .active');
  if (!selectedSticker) {
    console.warn('[WARNING] No active sticker found.');
    return null;
  }
  console.log(`[DEBUG] Active sticker found: ${selectedSticker.getAttribute('data-id')}`);
  return selectedSticker;
};

const uploadPhoto = () => {
  const canvasCopyElement = document.getElementById('canvasCopy');
  const photo = document.getElementById('photo');
  photo.value = canvasCopyElement.toDataURL();
  console.log('[DEBUG] Photo data URL set.');
  return true;
};

document.addEventListener('DOMContentLoaded', () => {
  const defaultSticker = document.querySelector('#sticker_div img[data-id="1"]');
  if (defaultSticker) {
    defaultSticker.classList.add('active');
    document.getElementById('sticker').value = defaultSticker.getAttribute('data-id');
    overlayImage.src = defaultSticker.src;
    console.log(`[DEBUG] Default sticker set to: ${defaultSticker.src}, sticker ID: ${defaultSticker.getAttribute('data-id')}`);
  }
});

document.getElementById('uploadPic').onchange = (event) => {
  const output = document.getElementById('upload_img');
  const saveButton = document.getElementById('uploadBtt');
  output.src = URL.createObjectURL(event.target.files[0]);
  output.style.display = 'block';
  saveButton.style.display = 'block';
};

uploadButton.addEventListener('click', () => {
  const currentSticker = stickerSelector();
  document.getElementById('sticker').value = currentSticker ? currentSticker.getAttribute('data-id') : '1';
  console.log('Current sticker ID: ', document.getElementById('sticker').value);

  context.clearRect(0, 0, canvas.width, canvas.height);
  context.drawImage(picture, 0, 0, 640, 480);
  if (currentSticker) {
    context.drawImage(currentSticker, 0, 0, 265, 250);
  }
  const canvasContext = canvasCopy.getContext('2d');
  canvasContext.clearRect(0, 0, canvasCopy.width, canvasCopy.height);
  canvasContext.drawImage(picture, 0, 0, 640, 480);
  console.log('[DEBUG] Images drawn on canvas.');
});
