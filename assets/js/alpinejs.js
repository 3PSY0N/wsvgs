import Alpine from 'alpinejs';
import ClipboardJS from "clipboard";
import EasyMDE from "easymde";

window.Alpine = Alpine
Alpine.start();

/**
 * Clipboardjs to copy svg code
 */
const clipboard = new ClipboardJS('.cpyBtn');
clipboard.on('success', function (event) {
    let successNotif = document.createElement('div');

    successNotif.classList.add('absolute', 'bg-green-400', 'rounded-t-lg', 'top-0', 'left-0', 'right-0');
    successNotif.innerText = "SVG copied";

    event.trigger.insertAdjacentElement('beforebegin', successNotif);

    setTimeout(hideSuccessNotif, 2500);

    function hideSuccessNotif() {
        successNotif.classList.add('text-slate-100', 'bg-slate-100', 'dark:bg-slate-700/70', 'dark:text-slate-700/70',  'transition-all', 'duration-300');
        setTimeout(removeSuccessNotif, 280);
    }
    function removeSuccessNotif() {
        successNotif.remove();
    }
});

/*
* Create modal for icons
*/
const modalsPreview = document.querySelectorAll("[data-modalpreview]");

modalsPreview.forEach(function (trigger) {

    trigger.addEventListener("click", function (event) {
        event.preventDefault();

        const modalValue = trigger.firstElementChild.valueOf().outerHTML;
        const outlineSvg = trigger.dataset.iconclass;

        const modal = document.getElementById(trigger.dataset.modalpreview);
        modal.classList.add('!visible', 'opacity-100');

        const svgDiv = document.createElement('div');
        svgDiv.classList.add('modalSvg', outlineSvg ? outlineSvg : null);
        svgDiv.innerHTML = modalValue;

        modal.appendChild(svgDiv);

        const exits = modal.querySelectorAll(".modal-exit");
        exits.forEach(function (exit) {
            exit.addEventListener("click", function (event) {
                event.preventDefault();
                modal.classList.remove('!visible', 'opacity-100');
                svgDiv.remove()
            });
        });
    });
});


const easyMDEOptions = {
    minHeight: "90px",
    toolbar: false,
    status: false,
    lineNumbers: true,
}
document.querySelectorAll('textarea').forEach(function (textElement) {
    const element = { element: textElement };
    const mergeOptions = { ...easyMDEOptions, ...element };

    new EasyMDE(mergeOptions);
})
