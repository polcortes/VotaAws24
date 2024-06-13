addEventListener('load', () => {
    const questions = JSON.parse(document.getElementById("questions-to-js").value);
    const pieButt = document.getElementById('pie-chart')
    const columnButt = document.getElementById('column-chart')

    const pieChartCont = document.querySelector("#pie-chart-cont")
    const columnChartCont = document.querySelector("#column-chart-cont")

    let series = []
    let labels = []

    let columnData = []
  
    for (const question of questions) {
      series.push(question.countOfVotes)
      labels.push(question.optionText)
      columnData.push({
        x: question.optionText,
        y: question.countOfVotes
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
      series: series,
      labels: labels
    }

    let columnOptions = {
      theme: {
        mode: document.documentElement.dataset.theme,
      },
      chart: {
          type: 'bar',
          width: '100%',
          height: '100%',
          toolbar : {
              show: false
          }
      },
      series: [{
          data: columnData
      }],
      xaxis: {
          labels: {
              style: {
                  colors: '#D9D9D9'
              }
          }
      },
      yaxis: {
          labels: {
              style: {
                  colors: '#D9D9D9'
              }
          }
      },
      tooltip: {
          show: false
      }
    }


    var pieChart = new ApexCharts(pieChartCont, pieOptions);
    pieChart.render()
    var columnChart = new ApexCharts(columnChartCont, columnOptions);
    columnChart.render()

    pieButt.addEventListener('click', function() {
        if (!this.classList.contains("active-chart-butt")) {
            this.classList.add("active-chart-butt")
            columnButt.classList.remove("active-chart-butt")

            pieChartCont.classList.add("active-chart")
            columnChartCont.classList.remove("active-chart")
        }
    })

    columnButt.addEventListener('click', function() {
        if (!this.classList.contains("active-chart-butt")) {
            this.classList.add("active-chart-butt")
            pieButt.classList.remove("active-chart-butt")
            
            columnChartCont.classList.add("active-chart")
            pieChartCont.classList.remove("active-chart")
        }
    })

    window.addEventListener('resize', () => {
      pieChart.updateOptions({
        chart: {
          width: '100%',
          height: '100%',
        }
      });

      columnChart.updateOptions({
        chart: {
          width: '100%',
          height: '100%',
        }
      });
    })

    document.addEventListener('themechange', () => {
      pieChart.updateOptions({
        theme: {
          mode: document.documentElement.dataset.theme,
        }
      });

      columnChart.updateOptions({
        theme: {
          mode: document.documentElement.dataset.theme,
        }
      });
    });
  });