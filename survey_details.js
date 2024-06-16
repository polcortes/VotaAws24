addEventListener('load', () => {
  const questions = JSON.parse(document.getElementById('questions-to-js').value)
  const pieButt = document.getElementById('pie-chart')
  const columnButt = document.getElementById('column-chart')

  // const pieChartCont = document.querySelector('#pie-chart-cont')
  // const columnChartCont = document.querySelector('#column-chart-cont')

  const chartContainer = document.getElementById('chart-container')

  const series = []
  const labels = []

  for (const question of questions) {
    series.push(question.countOfVotes)
    labels.push(question.optionText)
    // columnData.push({
    //   x: question.optionText,
    //   y: question.countOfVotes,
    // })
  }

  let theme = document.documentElement.dataset.theme;
  if (!theme) {
    document.documentElement.dataset.theme = 'dark';
    theme = 'dark';
  }

  if (theme === 'light') Chart.defaults.color = '#d9d9d9';
  if (theme === 'dark') Chart.defaults.color = '#272727';

  const data = {
    labels,
    datasets: [{
      data: series,
    }]
  };

  const pieOptions = {
    type: 'pie',
    data: data,
    options: {
      responsive: true,
      mantainAspectRatio: true
    }
  };

  const barOptions = {
    type: 'bar',
    data: data,
    options: {
      responsive: true,
      mantainAspectRatio: true,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    },
  };

  // let pieOptions = {
  //   theme: {
  //     mode: document.documentElement.dataset.theme,
  //   },
  //   chart: {
  //     type: 'pie',
  //     //size: '65%',
  //     width: '100%',
  //     height: '100%',
  //   },
  //   plotOptions: {
  //     pie: {
  //       customScale: 1,
  //     },
  //     donut: {
  //       labels: {
  //         show: true,
  //         name: {
  //           show: true,
  //           fontFamily: 'Montserrat',
  //           color: '#D9D9D9',
  //         },
  //         value: {
  //           show: true,
  //           fontFamily: 'Montserrat',
  //           color: '#D9D9D9',
  //         },
  //       },
  //     },
  //   },
  //   legend: {
  //     fontFamily: 'Montserrat',
  //     fontSize: '20px',
  //     color: '#D9D9D9',
  //     position: 'bottom',
  //     labels: {
  //       color: '#D9D9D9',
  //     },
  //   },
  //   series: series,
  //   labels: labels,
  // }

  // let columnOptions = {
  //   theme: {
  //     mode: document.documentElement.dataset.theme,
  //   },
  //   chart: {
  //     type: 'bar',
  //     //width: '100%',
  //     //height: '100%',
  //     toolbar: {
  //       show: false,
  //     },
  //   },
  //   series: [
  //     {
  //       data: columnData,
  //     },
  //   ],
  //   xaxis: {
  //     labels: {
  //       style: {
  //         colors: '#D9D9D9',
  //       },
  //     },
  //   },
  //   yaxis: {
  //     labels: {
  //       style: {
  //         colors: '#D9D9D9',
  //       },
  //     },
  //   },
  //   tooltip: {
  //     show: false,
  //   },
  // }

  // var pieChart = new ApexCharts(pieChartCont, pieOptions)
  // pieChart.render()
  // var columnChart = new ApexCharts(columnChartCont, columnOptions)
  // columnChart.render()

  // let chart = new ApexCharts(chartContainer, pieOptions);
  const ctx = document.getElementById('chart-container');
  new Chart(
    ctx,
    pieOptions
  );

  pieButt.addEventListener('click', function () {
    Chart.getChart("chart-container").destroy()

    new Chart(
      ctx,
      pieOptions
    )

    this.disabled = true;
    columnButt.disabled = false;
  })

  columnButt.addEventListener('click', function () {
    Chart.getChart("chart-container").destroy()

    new Chart(
      ctx,
      barOptions
    )

    this.disabled = true;
    pieButt.disabled = false;
  })
  //
  // window.addEventListener('resize', () => {
  //   pieChart.updateOptions({
  //     chart: {
  //       width: '100%',
  //       height: '100%',
  //     },
  //   })

  //   columnChart.updateOptions({
  //     chart: {
  //       width: '100%',
  //       height: '100%',
  //     },
  //   })

  //   pieChart.updateOptions({

  //   })
  // })

  document.addEventListener('themechange', () => {
    if (!theme) {
      document.documentElement.dataset.theme = 'dark';
      theme = 'dark';
    }
  
    if (theme === 'light') Chart.defaults.color = '#d9d9d9';
    if (theme === 'dark') Chart.defaults.color = '#272727';
  })
})
