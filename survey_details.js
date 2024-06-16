addEventListener('load', () => {
  const questions = JSON.parse(document.getElementById('questions-to-js').value)
  const pieButt = document.getElementById('pie-chart')
  const columnButt = document.getElementById('column-chart')

  // const pieChartCont = document.querySelector('#pie-chart-cont')
  // const columnChartCont = document.querySelector('#column-chart-cont')

  const chartContainer = document.getElementById('chart-container')

  let series = []
  let labels = []

  let columnData = []

  for (const question of questions) {
    series.push(question.countOfVotes)
    labels.push(question.optionText)
    columnData.push({
      x: question.optionText,
      y: question.countOfVotes,
    })
  }

  let pieOptions = {
    theme: {
      mode: document.documentElement.dataset.theme,
    },
    chart: {
      type: 'pie',
      //size: '65%',
      width: '100%',
      height: '100%',
    },
    plotOptions: {
      pie: {
        customScale: 1,
      },
      donut: {
        labels: {
          show: true,
          name: {
            show: true,
            fontFamily: 'Montserrat',
            color: '#D9D9D9',
          },
          value: {
            show: true,
            fontFamily: 'Montserrat',
            color: '#D9D9D9',
          },
        },
      },
    },
    legend: {
      fontFamily: 'Montserrat',
      fontSize: '20px',
      color: '#D9D9D9',
      position: 'bottom',
      labels: {
        color: '#D9D9D9',
      },
    },
    series: series,
    labels: labels,
  }

  let columnOptions = {
    theme: {
      mode: document.documentElement.dataset.theme,
    },
    chart: {
      type: 'bar',
      //width: '100%',
      //height: '100%',
      toolbar: {
        show: false,
      },
    },
    series: [
      {
        data: columnData,
      },
    ],
    xaxis: {
      labels: {
        style: {
          colors: '#D9D9D9',
        },
      },
    },
    yaxis: {
      labels: {
        style: {
          colors: '#D9D9D9',
        },
      },
    },
    tooltip: {
      show: false,
    },
  }

  // var pieChart = new ApexCharts(pieChartCont, pieOptions)
  // pieChart.render()
  // var columnChart = new ApexCharts(columnChartCont, columnOptions)
  // columnChart.render()

  let chart = new ApexCharts(chartContainer, pieOptions);

  pieButt.addEventListener('click', async function () {
    await chart.updateOptions(pieOptions, false, true, false);
    this.disabled = true;

    const theme = document.documentElement.dataset.theme
    if (theme === 'light') {
      document.getElementsByClassName('survey-details-chart')[0].style.backgroundColor = '#D9D9D9 !important'
    }
  })

  columnButt.addEventListener('click', async function () {
    await chart.updateOptions(columnOptions, false, true, false);
    this.disabled = true;

    const theme = document.documentElement.dataset.theme
    if (theme === 'light') {
      document.querySelector('.survey-details-chart').style.backgroundColor = '#272727 !important'
    }
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
    pieChart.updateOptions({
      theme: {
        mode: document.documentElement.dataset.theme,
      },
    })

    columnChart.updateOptions({
      theme: {
        mode: document.documentElement.dataset.theme,
      },
    })
  })
})
