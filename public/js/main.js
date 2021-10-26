//remove a garden from the list in the index view
const gardens = document.getElementById("gardens");
if (gardens) {
  gardens.addEventListener("click", (e) => {
    if (e.target.className === "btn btn-danger delete-garden") {
      if (confirm("sure?")) {
        const id = e.target.getAttribute("data-id");

        fetch(`garden/delete/${id}`, { method: "DELETE" }).then((res) =>
          window.location.reload()
        );
      }
    }
  });
}

//remove an image from the list in the upload view
const images = document.getElementById("images");
if (images) {
  images.addEventListener("click", (e) => {
    if (e.target.className === "btn btn-danger delete-image") {
      if (confirm("sure?")) {
        const id = e.target.getAttribute("data-id");
        const file = e.target.getAttribute("data-file");

        fetch(`../remove/${id}/${file}`, { method: "DELETE" }).then((res) =>
          window.location.reload()
        );
      }
    }
  });
}
