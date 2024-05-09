let lis = Array.from($(".status_color"));
lis.forEach(function (value) {
  if (value.innerHTML == "上架") {
    value.className = "text-status-true";
  } else if (value.innerHTML == "下架") {
    value.className = "text-status-false";
  }
});
