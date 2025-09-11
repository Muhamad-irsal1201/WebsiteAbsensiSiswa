// Toggle sidebar
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('toggle-btn');
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        content.classList.toggle('shifted');
    });
});

document.addEventListener("DOMContentLoaded", () => {
  const dropdown = document.querySelector(".dropdown");
  const toggle   = dropdown.querySelector(".dropdown-toggle");

  toggle.addEventListener("click", (e) => {
    e.preventDefault();
    dropdown.classList.toggle("open");
  });

  // klik di luar → tutup dropdown
  document.addEventListener("click", (e) => {
    if (!dropdown.contains(e.target)) {
      dropdown.classList.remove("open");
    }
  });
});

function exportExcel(selectedClass) {
    let table = document.querySelector(".attendance-table");
    let wb = XLSX.utils.table_to_book(table, {sheet:"Attendance"});
    XLSX.writeFile(wb, "Report_Attendance_Class_" + selectedClass + ".xlsx");
}

function exportPDF(selectedClass) {
    const { jsPDF } = window.jspdf;
    let doc = new jsPDF('l', 'pt', 'a4'); // landscape

    doc.setFontSize(16);
    doc.text("Result Attendance - Class " + selectedClass, 40, 40);

    doc.autoTable({ 
        html: ".attendance-table",
        startY: 60,
        styles: { fontSize: 10 },
        headStyles: { fillColor: [56,142,60] } // hijau header
    });

    doc.save("Report_Attendance_Class_" + selectedClass + ".pdf");
}

function printTable(selectedClass) {
    let printContent = document.querySelector(".attendance-table").cloneNode(true);

    // Tambahkan judul
    let title = document.createElement("h2");
    title.textContent = "Result Attendance - Class " + selectedClass;
    title.style.textAlign = "center";
    title.style.marginBottom = "15px";

    // Buat container sementara untuk print
    let container = document.createElement("div");
    container.appendChild(title);
    container.appendChild(printContent);

    let WinPrint = window.open('', '', 'width=900,height=600');
    WinPrint.document.write('<html><head><title>Print</title>');
    WinPrint.document.write('<link rel="stylesheet" href="../css/Attedance.css">'); 
    WinPrint.document.write('</head><body>');
    WinPrint.document.write(container.outerHTML);
    WinPrint.document.write('</body></html>');
    WinPrint.document.close();
    WinPrint.focus();
    WinPrint.print();
    WinPrint.close();
}
