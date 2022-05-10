window.onload = () => {
  console.log("On page load");

  let links = document.querySelectorAll("[data-delete]");

  for (link of links) {
    link.addEventListener("click", function (responce) {
      responce.preventDefault();
      console.log("Start 1");
      if (confirm("voulez-vous supprimer cett element ?")) {
        console.log("Start 2");

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
            console.log("Data");
            // console.log("data out");
            // console.log(data.body);

            console.log(data);
            // console.log(data.body);
            if (data.success) this.parentElement.remove();
            else {
            }
            window.location.reload();
            //alert(data.error);
          })
          .catch((e) => {
            console.log("Error");
            console.log(e);
            //alert(e)
          });
      }
    });
  }
};
