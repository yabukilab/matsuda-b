const images = [
  ["S__98484272_0.jpg", "sky.jpg", "S__98484273_0.jpg"], // お店1の画像リスト
  ["mi2.jpg", "mi3.jpg", "mi4.jpg", "mi5.jpt"]  // お店2の画像リスト
];

let currentImageIndex = [0, 0]; // 各お店の現在の画像インデックス

function changeImage(shopIndex) {
  // 次の画像インデックスに更新
  currentImageIndex[shopIndex] = (currentImageIndex[shopIndex] + 1) % images[shopIndex].length;

  // 対応する画像を切り替え
  const photoId = `shop${shopIndex + 1}-photo`;
  document.getElementById(photoId).src = images[shopIndex][currentImageIndex[shopIndex]];
}
