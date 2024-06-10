let btnPersonalPage = document.querySelector(".personalPageButtom");

btnPersonalPage.addEventListener("click", () => {
    let timerInterval;
    Swal.fire({
        title: "Espere mientras ubicamos su pagina",
        html: "",
        timer: 20000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
            const timer = Swal.getPopup().querySelector("b");
            timerInterval = setInterval(() => {
                timer.textContent = `${Swal.getTimerLeft()}`;
            }, 100);
        },
        willClose: () => {
            clearInterval(timerInterval);
        },
    }).then((result) => {
        /* Read more about handling dismissals below */
        if (result.dismiss === Swal.DismissReason.timer) {
            console.log("I was closed by the timer");
        }
    });
});

// Swal.fire({
//   title: "Sweet!",
//   text: "Modal with a custom image.",
//   imageUrl: "https://unsplash.it/400/200",
//   imageWidth: 400,
//   imageHeight: 200,
//   imageAlt: "Custom image"
// });
