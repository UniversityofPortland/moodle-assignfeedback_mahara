<?php

class assign_feedback_mahara extends assign_feedback_plugin {

  /**
   * @see parent
   * @return string
   */
  public function get_name() {
    return get_string('pluginfile', 'assignsubmission_mahara');
  }

  /**
   * @see parent
   */
  public function is_empty(stdClass $grade) {
    return false;
  }

  /**
   * @see parent
   */
  public function has_user_summary() {
    return false;
  }

  /**
   * @see parent
   */
  public function get_settings(MoodleQuickForm $form) {
    $mahara = $form->elementExists('assignsubmission_mahara_enabled');
    if (!$mahara) {
      $form->setDefault('assignfeedback_mahara_enabled', 0);
      $form->hardFreeze('assignfeedback_mahara_enabled');
    } else {
      if ($form->getElementType('assignfeedback_mahara_enabled') == 'selectyesno') {
        $form->disabledIf('assignfeedback_mahara_enabled', 'assignsubmission_mahara_enabled', 'eq', '0');
      } else {
        $form->disabledIf('assignfeedback_mahara_enabled', 'assignsubmission_mahara_enabled');
      }
    }
  }

  /**
   * @see parent
   */
  public function save(stdClass $grade, stdClass $data) {
    if ($release = $this->prepare_release($grade)) {
      $plugin = $this;
      return $release
        ->map(
          function($tuple) use ($plugin, $data) {
            list($mahara, $event, $portfolio) = $tuple;

            $outcomes = $plugin->process_outcomes_from_form($event->grade, $data);
            return $plugin->complete_release($mahara, $event, $portfolio, $outcomes);
          })
        ->getOrElse(true);
    }

    return true;
  }

  /**
   * Prepares the release scenario
   *
   * @param stdClass $grade
   * @return Model_Option|null
   */
  private function prepare_release($grade) {
    $event = new stdClass;
    $event->assignment = $this->assignment;
    $event->grade = $grade;
    $event->submission = $this->get_submission_for_grade($grade);

    $mahara = $event->assignment->get_submission_plugin_by_type('mahara');

    if ($submitted = $mahara->get_portfolio_record($event->submission) ) {
      return $mahara
        ->get_service()
        ->get_local_portfolio($submitted->portfolio)
        ->map(function($portfolio) use ($mahara, $event) {
          return array($mahara, $event, $portfolio);
        });
    }

    return null;
  }

  /**
   * Completes the commone release scenario
   *
   * @param mixed $mahara
   * @param stdClass $event
   * @param stdClass $portfolio
   * @param array $outcomes
   * @return boolean
   */
  public function complete_release($mahara, $event, $portfolio, $outcomes) {
    return $mahara
      ->get_service()
      ->request_release_submitted_view(
        $event->grade->grader,
        $portfolio->page,
        $outcomes
      )
      ->withRight()
      ->each(
        function() use ($event) {
          events_trigger('assign_mahara_grade_submitted', $event);
        })
      ->isRight();
  }

  /**
   * @see parent
   */
  public function supports_quickgrading() {
    return true;
  }

  /**
   * @see parent
   */
  public function save_quickgrading_changes($userid, $grade) {
    if ($release = $this->prepare_release($grade)) {
      $plugin = $this;
      return $release
        ->map(
          function($tuple) use ($grade, $plugin) {
            list($mahara, $event, $portfolio) = $tuple;

            $outcomes = $plugin->process_outcomes_from_quickgrading($grade);
            return $plugin->complete_release($mahara, $event, $portfolio, $outcomes);
          })
        ->getOrElse(true);
    }
    return true;
  }

  /**
   * Get user grading info
   *
   * @param $grade
   * @return grading_info
   */
  public function get_user_grade_info($grade) {
    return $grading_info = grade_get_grades(
      $this->assignment->get_course()->id,
      'mod',
      'assign',
      $this->assignment->get_instance()->id,
      $grade->userid
    );
  }

  /**
   * Process outcome data from quick grading
   *
   * @param $grade
   * @return array
   */
  public function process_outcomes_from_quickgrading($grade) {
    $grading_info = $this->get_user_grade_info($grade);

    $viewoutcomes = array();
    if (!empty($grading_info->outcomes)) {
      foreach ($grading_info->outcomes as $outcomeid => $outcome) {
        $newoutcome_name = "outcome_{$outcomeid}_{$grade->userid}";
        $oldoutcome = $outcome->grades[$grade->userid]->grade;
        $newoutcome = optional_param($newoutcome_name, -1, PARAM_INT);

        $scale = make_grades_menu(-$outcome->scaleid);
        if ($oldoutcome == $newoutcome || !isset($scale[$newoutcome])) {
          continue;
        }

        foreach ($scale as $k => $v) {
          $scale[$k] = array('name' => $v, 'value' => $k);
        }

        $viewoutcomes[] = array(
          'name' => $outcome->name,
          'scale' => $scale,
          'grade' => $newoutcome,
        );
      }
    }

    return $viewoutcomes;
  }

  /**
   * Process outcome data from a form
   *
   * @param $grade
   * @param stdClass $formdata
   * @return array
   */
  public function process_outcomes_from_form($grade, $formdata) {
    $grading_info = $this->get_user_grade_info($grade);
    $viewoutcomes = array();

    if (!empty($grading_info->outcomes)) {
      foreach ($grading_info->outcomes as $index => $outcome) {
        $name = "outcome_$index";
        $oldoutcome = $outcome->grades[$grade->userid]->grade;
        $scale = make_grades_menu(-$outcome->scaleid);

        if (
          !isset($formdata->{$name}[$grade->userid]) ||
          $oldoutcome == $formdata->{$name}[$grade->userid] ||
          !isset($scale[$formdata->{$name}[$grade->userid]])
        ) {
          continue;
        }

        foreach ($scale as $k => $v) {
          $scale[$k] = array('name' => $v, 'value' => $k);
        }

        $viewoutcomes[] = array(
          'name' => $outcome->name,
          'scale' => $scale,
          'grade' => $formdata->{$name}[$grade->userid],
        );
      }
    }

    return $viewoutcomes;
  }

  /**
   * Gets the submission for the grade
   *
   * @param stdClass $grade
   * @return stdClass $submission
   */
  public function get_submission_for_grade($grade) {
    global $DB;

    return $DB->get_record('assign_submission', array(
      'assignment' => $this->assignment->get_instance()->id,
      'userid' => $grade->userid,
    ));
  }
}
