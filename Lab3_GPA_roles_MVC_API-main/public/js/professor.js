/* global $, window */
(function () {
  'use strict';

  const base = window.APP_BASE || '';

  function apiUrl(params) {
    const q = $.param(params);
    return base + '/api/grades.php?' + q;
  }

  let currentSemester = null;
  let currentCourse = null;

  function setStatus(msg, isError) {
    const $el = $('#pg-status');
    $el.text(msg || '');
    $el.toggleClass('text-danger', !!isError);
    $el.toggleClass('text-muted', !isError);
  }

  function loadCourses(semesterId) {
    currentSemester = semesterId;
    currentCourse = null;
    $('#pg-course').prop('disabled', true).html('<option value="">Loading…</option>');
    $('#pg-save').prop('disabled', true);
    $('#pg-tbody').html('<tr><td colspan="2" class="text-muted p-4">Select a course.</td></tr>');
    if (!semesterId) {
      $('#pg-course').html('<option value="">Select semester first…</option>');
      return;
    }
    $.getJSON(apiUrl({ action: 'courses', semester_id: semesterId }))
      .done(function (res) {
        const opts = ['<option value="">Choose course…</option>'];
        (res.courses || []).forEach(function (c) {
          opts.push(
            '<option value="' +
              c.course_id +
              '">' +
              $('<div>').text(c.course_name).html() +
              ' (' +
              c.credits +
              ' cr)</option>'
          );
        });
        $('#pg-course').html(opts.join('')).prop('disabled', false);
        if ((res.courses || []).length === 0) {
          setStatus('No assigned courses for this semester.', false);
        } else {
          setStatus('', false);
        }
      })
      .fail(function (xhr) {
        let msg = 'Could not load courses.';
        try {
          const j = JSON.parse(xhr.responseText);
          if (j.error) msg = j.error;
        } catch (e) { /* ignore */ }
        setStatus(msg, true);
        $('#pg-course').html('<option value="">Error</option>');
      });
  }

  function gradeSelect(selected) {
    const letters = ['', 'A', 'B', 'C', 'D', 'F'];
    const parts = letters.map(function (g) {
      const sel = g === (selected || '') ? ' selected' : '';
      const label = g === '' ? '—' : g;
      return '<option value="' + g + '"' + sel + '>' + label + '</option>';
    });
    return '<select class="form-select grade-select">' + parts.join('') + '</select>';
  }

  function loadStudents(semesterId, courseId) {
    currentCourse = courseId;
    $('#pg-save').prop('disabled', true);
    $('#pg-tbody').html('<tr><td colspan="2" class="text-muted p-4">Loading…</td></tr>');
    if (!courseId) {
      $('#pg-tbody').html('<tr><td colspan="2" class="text-muted p-4">Select a course.</td></tr>');
      return;
    }
    $.getJSON(apiUrl({ action: 'students', semester_id: semesterId, course_id: courseId }))
      .done(function (res) {
        const rows = res.students || [];
        if (rows.length === 0) {
          $('#pg-tbody').html('<tr><td colspan="2" class="text-muted p-4">No enrolled students.</td></tr>');
          $('#pg-save').prop('disabled', true);
          return;
        }
        const html = rows
          .map(function (s) {
            const g = s.grade != null ? String(s.grade) : '';
            return (
              '<tr data-student-id="' +
              s.student_id +
              '"><td class="fw-semibold">' +
              $('<div>').text(s.student_name).html() +
              '</td><td>' +
              gradeSelect(g) +
              '</td></tr>'
            );
          })
          .join('');
        $('#pg-tbody').html(html);
        $('#pg-save').prop('disabled', false);
        setStatus('', false);
      })
      .fail(function (xhr) {
        let msg = 'Could not load students.';
        try {
          const j = JSON.parse(xhr.responseText);
          if (j.error) msg = j.error;
        } catch (e) { /* ignore */ }
        setStatus(msg, true);
        $('#pg-tbody').html('<tr><td colspan="2" class="text-danger p-4">' + $('<div>').text(msg).html() + '</td></tr>');
      });
  }

  function collectGrades() {
    const grades = [];
    $('#pg-tbody tr').each(function () {
      const sid = $(this).data('student-id');
      if (!sid) return;
      const g = $(this).find('select').val() || '';
      grades.push({ student_id: sid, grade: g });
    });
    return grades;
  }

  $('#pg-semester').on('change', function () {
    loadCourses($(this).val() || null);
  });

  $('#pg-course').on('change', function () {
    const cid = $(this).val();
    if (currentSemester && cid) {
      loadStudents(currentSemester, cid);
    } else {
      $('#pg-tbody').html('<tr><td colspan="2" class="text-muted p-4">Select a course.</td></tr>');
      $('#pg-save').prop('disabled', true);
    }
  });

  $('#pg-save').on('click', function () {
    if (!currentSemester || !currentCourse) return;
    const payload = JSON.stringify({
      semester_id: currentSemester,
      course_id: currentCourse,
      grades: collectGrades(),
    });
    setStatus('Saving…', false);
    $('#pg-save').prop('disabled', true);
    $.ajax({
      url: apiUrl({ action: 'save' }),
      method: 'POST',
      contentType: 'application/json; charset=utf-8',
      data: payload,
      dataType: 'json',
    })
      .done(function () {
        setStatus('Saved successfully.', false);
        $('#pg-save').prop('disabled', false);
      })
      .fail(function (xhr) {
        let msg = 'Save failed.';
        try {
          const j = JSON.parse(xhr.responseText);
          if (j.error) msg = j.error;
        } catch (e) { /* ignore */ }
        setStatus(msg, true);
        $('#pg-save').prop('disabled', false);
      });
  });
})();
