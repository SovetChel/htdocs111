window.addEventListener("load", () => {
    const conainerOrderCancel = document.querySelector("#order-cancel");
    const btClose = conainerOrderCancel.querySelector("#close-order-cancel");
    const btSubmit = conainerOrderCancel.querySelector("form input[type='submit']");
    const pName = conainerOrderCancel.querySelector("form .name");
    const pPrice = conainerOrderCancel.querySelector("form .price");
    const inputIDOrder = conainerOrderCancel.querySelector("form #id-order");

    btClose.onclick = () => {
        btSubmit.disabled = true;
        document.body.style.overflow = null;
        conainerOrderCancel.classList.remove("order-cancel--show");
    };

    conainerOrderCancel.onmousedown = function(e) {
        if (e.target == this && e.button === 0) {
            btClose.onclick();
        };
    };

    document.querySelectorAll(".order-cancel").forEach(btCancel => {
        const name = btCancel.parentElement.querySelector(".name").textContent;
        const price = btCancel.parentElement.querySelector(".price").textContent;
        
        btCancel.onclick = () => {
            inputIDOrder.value = btCancel.id;
            pName.textContent = `Товар: ${name}`;
            pPrice.textContent = `Цена: ${price}`;

            document.body.style.overflow = "hidden";
            conainerOrderCancel.classList.add("order-cancel--show");
            btSubmit.disabled = false;
        };
    });
});