document.addEventListener("DOMContentLoaded", function () {
  const popupContainer = document.getElementById("image-popup-container");
  const popupImage = document.getElementById("image-popup-img");
  const closePopup = document.getElementById("image-close-popup");

  const popupLinks = document.querySelectorAll(".popup-link");

  popupLinks.forEach(function (link) {
    link.addEventListener("click", function (event) {
      event.preventDefault();
      const imageURL = this.getAttribute("href");
      popupImage.setAttribute("src", imageURL);
      popupContainer.style.display = "flex";
    });
  });

  popupContainer.addEventListener("click", function (event) {
    if (event.target === popupContainer) {
      popupContainer.style.display = "none";
    }
  });

  closePopup.addEventListener("click", function () {
    popupContainer.style.display = "none";
  });
});

function deletePostPopup(button) {
  const popupContainer = document.getElementById("deletepost-popup-container");
  const closePopup = document.getElementById("deletepost-close-popup");
  const postId = button.closest('.post').getAttribute('data-post-id');
  popupContainer.style.display = "flex";

  const idInput = popupContainer.querySelector('input[name="post_id"]');
  idInput.value = postId;

  popupContainer.addEventListener("click", function (event) {
    if (event.target === popupContainer) {
      popupContainer.style.display = "none";
    }
  });

  closePopup.addEventListener("click", function () {
    popupContainer.style.display = "none";
  });
}