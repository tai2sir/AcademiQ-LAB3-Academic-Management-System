/* global $, window, Chart */
(function () {
  'use strict';

  const base = window.APP_BASE || '';

  function apiUrl(params) {
    return base + '/api/gpa.php?' + $.param(params);
  }

  function gpaClass(gpa) {
    if (gpa >= 3.7) return 'gpa-excellent';
    if (gpa >= 3.0) return 'gpa-good';
    if (gpa >= 2.0) return 'gpa-fair';
    return 'gpa-low';
  }

  function loadDashboard() {
    $.getJSON(apiUrl({ action: 'current' }))
      .done(function (res) {
        $('#sd-loading').addClass('d-none');
        if (!res.active) {
          $('#sd-empty').removeClass('d-none').text(res.message || 'No active semester.');
          return;
        }
        if (!res.enrolled) {
          $('#sd-empty')
            .removeClass('d-none')
            .text('You are not enrolled in the active semester.');
          return;
        }
        $('#sd-content').removeClass('d-none');
        const gpa = typeof res.gpa === 'number' ? res.gpa : parseFloat(res.gpa);
        $('#sd-gpa-value').text(gpa.toFixed(2));
        $('#sd-semester-label').text(
          (res.semester && res.semester.label ? res.semester.label : '') +
            (res.semester && res.semester.academic_year ? ' · ' + res.semester.academic_year : '')
        );
        const $card = $('#sd-gpa-card');
        $card.removeClass('gpa-excellent gpa-good gpa-fair gpa-low');
        $card.addClass(gpaClass(gpa));

        const tbody = (res.courses || [])
          .map(function (c) {
            const grade = c.grade != null && c.grade !== '' ? c.grade : '—';
            const prof = c.professor_name || '—';
            return (
              '<tr><td>' +
              $('<div>').text(c.course_name).html() +
              '</td><td>' +
              $('<div>').text(String(c.credits)).html() +
              '</td><td>' +
              $('<div>').text(prof).html() +
              '</td><td><span class="fw-semibold">' +
              $('<div>').text(grade).html() +
              '</span></td></tr>'
            );
          })
          .join('');
        $('#sd-courses-body').html(tbody || '<tr><td colspan="4" class="text-muted">No courses in this semester.</td></tr>');
      })
      .fail(function () {
        $('#sd-loading').addClass('d-none');
        $('#sd-empty').removeClass('d-none').text('Could not load dashboard.');
      });
  }

  let chartInstance = null;

  function loadHistory() {
    const $canvas = $('#sh-chart');
    if (!$canvas.length) return;

    $('#sh-csv').attr('href', apiUrl({ action: 'export' }));

    $.getJSON(apiUrl({ action: 'history' }))
      .done(function (res) {
        const rows = res.history || [];
        const $list = $('#sh-list');
        $list.empty();
        if (rows.length === 0) {
          $('#sh-chart-empty').removeClass('d-none');
          $canvas.addClass('d-none');
          return;
        }
        $('#sh-chart-empty').addClass('d-none');
        $canvas.removeClass('d-none');

        rows.forEach(function (r) {
          const label = r.label + ' · ' + r.academic_year;
          const gpa = parseFloat(r.gpa);
          const item =
            '<li class="list-group-item d-flex justify-content-between align-items-center">' +
            '<span>' +
            $('<div>').text(label).html() +
            '</span>' +
            '<span class="badge gpa-pill ' +
            gpaClass(gpa) +
            '">' +
            gpa.toFixed(2) +
            '</span></li>';
          $list.append(item);
        });

        const labels = rows.map(function (r) {
          return r.label + ' ' + r.academic_year;
        });
        const data = rows.map(function (r) {
          return parseFloat(r.gpa);
        });

        const ctx = $canvas[0].getContext('2d');
        if (chartInstance) {
          chartInstance.destroy();
        }
        chartInstance = new Chart(ctx, {
          type: 'line',
          data: {
            labels: labels,
            datasets: [
              {
                label: 'GPA',
                data: data,
                borderColor: 'rgba(99, 102, 241, 1)',
                backgroundColor: 'rgba(99, 102, 241, 0.15)',
                fill: true,
                tension: 0.35,
                pointRadius: 4,
                pointHoverRadius: 6,
              },
            ],
          },
          options: {
            responsive: true,
            plugins: {
              legend: { display: false },
            },
            scales: {
              y: {
                min: 0,
                max: 4,
                ticks: { stepSize: 0.5 },
              },
            },
          },
        });
      })
      .fail(function () {
        $('#sh-chart-empty').removeClass('d-none').text('Could not load history.').show();
        $canvas.addClass('d-none');
      });
  }

  $(function () {
    if ($('#sd-gpa-card').length) {
      loadDashboard();
    }
    if ($('#sh-chart').length) {
      loadHistory();
    }
  });
})();
