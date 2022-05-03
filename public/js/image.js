window.onload = () => {
  let links = document.querySelectorAll("[data-delete]");

  for (link of links) {
    link.addEventListener("click", function (responce) {
      responce.preventDefault();
      if (confirm("voulez-vous supprimer cett image?")) {
        //console.log(this.dataset.token);

        fetch(this.getAttribute("href"), {
          method: "DELETE",
          headers: {
            "X-Requested-With": "XMLHttpRequest",
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ _token: this.dataset.token }),
        })
          //.then
          //fkjhjk
          //console.log(responce);
          // (response) => responce.json()
          //()
          .then((data) => {
            // console.log("data out");
            // console.log(data);
            // console.log(data.body);
            if (data.success) this.parentElement.remove();
            else alert(data.error);
          })
          .catch((e) => alert(e));
      }
    });
  }
};
