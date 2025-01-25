function downloadPDF() {
    var button = document.getElementById("downloadButton");
    var originalText = button.innerHTML;

    button.innerHTML =
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
    button.disabled = true;

    $.ajax({
        url: "/kalkulator/cetak_pdf",
        type: "GET",
        xhrFields: {
            responseType: "blob",
        },
        success: function (data) {
            // Membuat link untuk download
            var blob = new Blob([data], { type: "application/pdf" });
            var link = document.createElement("a");
            link.href = window.URL.createObjectURL(blob);
            link.download = "Anggaran_Report_" + new Date().getTime() + ".pdf";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            button.innerHTML = originalText;
            button.disabled = false;
        },
        error: function (error) {
            alert("Terjadi kesalahan: " + error.responseJSON.error);
            button.innerHTML = originalText;
            button.disabled = false;
        },
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const darkModeDropdown = document.getElementById("darkModeDropdown");

    const storedMode = localStorage.getItem("darkMode");
    const isDarkMode = storedMode === "enabled";

    if (isDarkMode) {
        enableDarkMode();
        darkModeDropdown.value = "dark";
    }

    darkModeDropdown.addEventListener("change", function () {
        const selectedMode = darkModeDropdown.value;
        if (selectedMode === "dark") {
            enableDarkMode();
            localStorage.setItem("darkMode", "enabled");
        } else {
            disableDarkMode();
            localStorage.setItem("darkMode", null);
        }
    });

    function enableDarkMode() {
        document.body.classList.add("dark-mode");
    }

    function disableDarkMode() {
        document.body.classList.remove("dark-mode");
    }
});
