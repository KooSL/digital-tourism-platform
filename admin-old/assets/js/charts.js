new Chart(document.getElementById("viewsChart"), {
  type: "bar",
  data: {
    labels: viewsLabels,
    datasets: [
      {
        label: "Views",
        data: viewsData,
        backgroundColor: "#0d6efd",
      },
    ],
  },
});

new Chart(document.getElementById("bookingChart"), {
  type: "line",
  data: {
    labels: bookingLabels,
    datasets: [
      {
        label: "Bookings",
        data: bookingData,
        borderColor: "#198754",
        tension: 0.4,
        fill: true,
      },
    ],
  },
});

new Chart(document.getElementById("typeChart"), {
  type: "pie",
  data: {
    labels: typeLabels,
    datasets: [
      {
        data: typeData,
        backgroundColor: ["#0d6efd", "#20c997"],
      },
    ],
  },
});

