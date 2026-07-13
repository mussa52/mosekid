document.addEventListener('DOMContentLoaded', function () {
  if (window.Chart) {
    const deptChart = document.getElementById('deptChart');
    if (deptChart) {
      new Chart(deptChart, {
        type: 'bar',
        data: {
          labels: ['Engineering', 'HR', 'Finance', 'Operations'],
          datasets: [{ label: 'Employees', data: [35, 18, 22, 15], backgroundColor: '#2563eb' }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
      });
    }

    const attendanceChart = document.getElementById('attendanceChart');
    if (attendanceChart) {
      new Chart(attendanceChart, {
        type: 'line',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
          datasets: [{ label: 'Attendance', data: [88, 91, 93, 90, 95, 96], borderColor: '#10b981', fill: false }]
        },
        options: { responsive: true }
      });
    }

    const payrollChart = document.getElementById('payrollChart');
    if (payrollChart) {
      new Chart(payrollChart, {
        type: 'doughnut',
        data: {
          labels: ['Salary', 'Bonus', 'Deductions'],
          datasets: [{ data: [70, 20, 10], backgroundColor: ['#2563eb', '#10b981', '#ef4444'] }]
        },
        options: { responsive: true }
      });
    }

    const leaveChart = document.getElementById('leaveChart');
    if (leaveChart) {
      new Chart(leaveChart, {
        type: 'pie',
        data: {
          labels: ['Approved', 'Pending', 'Rejected'],
          datasets: [{ data: [55, 25, 20], backgroundColor: ['#10b981', '#f59e0b', '#ef4444'] }]
        },
        options: { responsive: true }
      });
    }
  }
});
