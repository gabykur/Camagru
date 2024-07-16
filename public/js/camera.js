const canvas = document.getElementById('canvas');
const canvasCopy = document.getElementById('canvasCopy');
const context = canvas.getContext('2d');
const video = document.getElementById('video');
const snap = document.getElementById('snap');
const overlayImage = document.getElementById('overlay');
const stickerDisplay = document.getElementById('sticker_div');
const stickerImgs = stickerDisplay.getElementsByClassName('stickerImg');

const initializeDefaultSticker = () => {
  const defaultSticker = document.querySelector("#sticker_div img[data-id='1']");
  if (defaultSticker) {
    defaultSticker.classList.add('active');
    document.getElementById('sticker').value = defaultSticker.getAttribute('data-id');
    overlayImage.src = defaultSticker.src;
    console.log(`[DEBUG] Default sticker set to: ${defaultSticker.src}, sticker ID: ${defaultSticker.getAttribute('data-id')}`);
  }
};

const stickerSelector = () => {
  const selectedSticker = document.querySelector('#sticker_div .active');
  if (!selectedSticker) {
    console.warn('[WARNING] No active sticker found.');
    return null;
  }
  console.log(`[DEBUG] Active sticker found: ${selectedSticker.getAttribute('data-id')}`);
  return selectedSticker;
};

const takePhoto = () => {
  const canvas = document.getElementById('canvasCopy');
  const photo = document.getElementById('photo');
  photo.value = canvas.toDataURL();
  console.log('[DEBUG] Photo data URL set.');
  return true;
};

navigator.mediaDevices.getUserMedia({ audio: false, video: { width: 640, height: 480 } })
  .then(mediaStream => {
    video.srcObject = mediaStream;
    video.onloadedmetadata = () => {
      video.play();
      snap.style.display = 'block';
    };
  })
  .catch(err => {
    console.error('An error occurred!', err);
  });

document.addEventListener('DOMContentLoaded', () => {
  initializeDefaultSticker();
});

snap.addEventListener('click', () => {
  const currentSticker = stickerSelector();
  if (!currentSticker) {
    alert('Please select a sticker before taking a photo.');
    console.error('[ERROR] No sticker selected.');
    return;
  }
  document.getElementById('sticker').value = currentSticker.getAttribute('data-id');
  console.log('Current sticker ID:', currentSticker.getAttribute('data-id'));
  context.drawImage(video, 0, 0, 640, 480);
  context.drawImage(currentSticker, 0, 0, 265, 250);
  canvasCopy.getContext('2d').drawImage(video, 0, 0, 640, 480);
});

Array.from(stickerImgs).forEach(stickerImg => {
  stickerImg.addEventListener('click', () => {
    const activePhoto = document.querySelector('.active');
    if (activePhoto) {
      activePhoto.classList.remove('active');
    }
    stickerImg.classList.add('active');
    overlayImage.src = stickerImg.src;
    document.getElementById('sticker').value = stickerImg.getAttribute('data-id');
    console.log(`[DEBUG] Overlay image src set to: ${stickerImg.src}, sticker ID: ${stickerImg.getAttribute('data-id')}`);
  });
});