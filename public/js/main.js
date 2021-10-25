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

const images = document.getElementById("images");

if (images) {
  console.log("ja");
  images.addEventListener("click", (e) => {
    if (e.target.className === "btn btn-danger delete-image") {
      if (confirm("sure?")) {
        const id = e.target.getAttribute("data-id");
        const file = e.target.getAttribute("data-file");

        fetch(`garden/remove/${id}/${file}`, { method: "DELETE" }).then((res) =>
          window.location.reload()
        );
      }
    }
  });
}
