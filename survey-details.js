addEventListener('load', () => {
  const questions = JSON.parse(document.getElementById("questions-to-js").value);

  series = []
  labels = []

  for (const question of questions) {
    series.push(question.countOfVotes)
    labels.push(question.optionText)
  }

  let options = {
    chart: {
      type: 'pie'
    },
    plotOptions: {
      pie: {
        size: 400
      },
      donut: {
        labels: {
          show: true,
          name: {
            show: true,
            fontFamily: 'Montserrat',
            color: '#D9D9D9'
          },
          value: {
            show: true,
            fontFamily: 'Montserrat',
            color: '#D9D9D9'
          }
        }
      }
    },
    legend: {
      fontFamily: 'Montserrat',
      fontSize: '20px',
      color: '#D9D9D9',
      position: 'bottom',
      labels: {
        color: '#D9D9D9'
      }
    },
    series: [50, 20],
    labels: labels
  }

  var chart = new ApexCharts(document.querySelector("#survey-details-chart"), options);
  chart.render();
});