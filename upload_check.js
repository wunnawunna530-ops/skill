document.getElementById("videoInput").addEventListener("change", function () {
  const file = this.files[0];
  const video = document.createElement("video");
  const warning = document.getElementById("durationWarning");
  const submitBtn = document.getElementById("submitBtn");

  video.preload = "metadata";
  video.onloadedmetadata = function () {
    window.URL.revokeObjectURL(video.src);
    const duration = video.duration;

    // 120 seconds = 2 mins | 300 seconds = 5 mins
    if (duration < 120 || duration > 300) {
      warning.style.display = "block";
      submitBtn.disabled = true;
      submitBtn.style.opacity = "0.5";
    } else {
      warning.style.display = "none";
      submitBtn.disabled = false;
      submitBtn.style.opacity = "1";
    }
  };
  video.src = URL.createObjectURL(file);
});
