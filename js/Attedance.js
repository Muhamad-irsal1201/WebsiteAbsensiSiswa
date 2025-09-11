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

document.querySelectorAll(".attendance-table button").forEach(btn => {
    btn.addEventListener("click", function(){
        let row = this.closest("tr");
        let student_id = row.dataset.student;
        let week = this.dataset.week;
        let status = this.textContent;
        let className = row.children[1].textContent;
        let month = document.getElementById("month").value;

        fetch("save_attendance.php", {
            method: "POST",
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: `student_id=${student_id}&week=${week}&status=${status}&class=${className}&month=${month}`
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                // update cell week
                row.querySelector(`.week-${week}`).textContent = status;
            } else {
                alert("Gagal: " + data.error);
            }
        });
    });
});
