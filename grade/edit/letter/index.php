<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * List of grade letters.
 *
 * @package   core_grades
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->libdir.'/gradelib.php';

$contextid = optional_param('id', SYSCONTEXTID, PARAM_INT);
$action   = optional_param('action', '', PARAM_ALPHA);
$edit     = optional_param('edit', false, PARAM_BOOL); //are we editing?

$PAGE->set_url('/grade/edit/letter/index.php', array('id' => $contextid));

list($context, $course, $cm) = get_context_info_array($contextid);
$contextid = null;//now we have a context object throw away the $contextid from the params

//if viewing
if (!$edit) {
    if (!has_capability('moodle/grade:manage', $context) and !has_capability('moodle/grade:manageletters', $context)) {
        print_error('nopermissiontoviewletergrade');
    }
} else {//else we're editing
    require_capability('moodle/grade:manageletters', $context);
}

// <LSUGRADES> If custom percents are used, adhere to the decimal places set by the teacher for the item or course when converting to letter. If not, use the moodle default of 2 decimal places.
$custom = (bool) get_config('moodle', 'grade_letters_custom');
$decimals = $custom ? (int) get_config('moodle', 'grade_decimalpoints') : 2;
// </LSUGRADES>

$returnurl = null;
$editparam = null;
if ($context->contextlevel == CONTEXT_SYSTEM or $context->contextlevel == CONTEXT_COURSECAT) {
    require_once $CFG->libdir.'/adminlib.php';
    require_login();

    admin_externalpage_setup('letters');

    $admin = true;
    $returnurl = "$CFG->wwwroot/grade/edit/letter/index.php";
    $editparam = '?edit=1';
} else if ($context->contextlevel == CONTEXT_COURSE) {

    $PAGE->set_pagelayout('standard');//calling this here to make blocks display

    require_login($context->instanceid, false, $cm);

    $admin = false;
    $returnurl = $CFG->wwwroot.'/grade/edit/letter/index.php?id='.$context->id;
    $editparam = '&edit=1';

    // <LSUGRADES> Again, if the letter grade uses a custom percentage, then adhere to the decimal places the teacher has set for the item in question.
    if ($custom) {
        $item = grade_item::fetch(array('itemtype' => 'course', 'courseid' => $course->id));

        $decimals = $item ? $item->get_decimals() : $decimals;
    }
    // </LSUGRADES>

    $gpr = new grade_plugin_return(array('type'=>'edit', 'plugin'=>'letter', 'courseid'=>$course->id));
} else {
    print_error('invalidcourselevel');
}

$strgrades = get_string('grades');
$pagename  = get_string('letters', 'grades');

$letters = grade_get_letters($context);
$num = count($letters) + 3;

//if were viewing the letters
if (!$edit) {

    $data = array();
    $max = 100;
    foreach($letters as $boundary=>$letter) {
        $line = array();

        // <LSUGRADES> DO NOT assume 2 decimal places, instead use the values set by the teacher for the item in question.
        $line[] = format_float($max, $decimals).' %';
        $line[] = format_float($boundary, $decimals).' %';
        // </LSUGRADES>

        $line[] = format_string($letter);
        $data[] = $line;

        // <LSUGRADES> DO NOT assume 2 decimal places, instead use the values set by the teacher for the item in question.
        $max = $boundary - (1 / pow(10, $decimals));
        // </LSUGRADES>

    }

    print_grade_page_head($COURSE->id, 'letter', 'view', get_string('gradeletters', 'grades'));

    $stredit = get_string('editgradeletters', 'grades');
    $editlink = html_writer::nonempty_tag('div', html_writer::link($returnurl.$editparam, $stredit), array('class'=>'mdl-align'));
    echo $editlink;

    $table = new html_table();
    $table->head  = array(get_string('max', 'grades'), get_string('min', 'grades'), get_string('letter', 'grades'));

    // <LSUGRADES> Small changes to column widths.
    $table->size  = array('33%', '33%', '34%');
    // </LSUGRADES>

    $table->align = array('left', 'left', 'left');

    // <LSUGRADES> Small changes to column widths.
    $table->width = '40%';
    // </LSUGRADES>

    $table->data  = $data;
    $table->tablealign  = 'center';
    echo html_writer::table($table);

    echo $editlink;
} else { //else we're editing
    require_once('edit_form.php');

    $data = new stdClass();
    $data->id = $context->id;

    $i = 1;
    foreach ($letters as $boundary=>$letter) {
        $gradelettername = 'gradeletter'.$i;
        $gradeboundaryname = 'gradeboundary'.$i;

        $data->$gradelettername   = $letter;

        // <LSUGRADES> Either uses the standard grade boundary or uses the custom boundary rounded to the appropriate decimals
        $data->$gradeboundaryname = $custom ? format_float($boundary, $decimals) : (int) $boundary;
        // </LSUGRADES>

        $i++;
    }
    $data->override = $DB->record_exists('grade_letters', array('contextid' => $context->id));
    $mform = new edit_letter_form($returnurl.$editparam, array('num'=>$num, 'admin'=>$admin));
    $mform->set_data($data);

    if ($mform->is_cancelled()) {
        redirect($returnurl);
    } else if ($data = $mform->get_data()) {
        if (!$admin and empty($data->override)) {
            $DB->delete_records('grade_letters', array('contextid' => $context->id));
            redirect($returnurl);
        }

        $letters = array();
        for($i=1; $i<$num+1; $i++) {
            $gradelettername = 'gradeletter'.$i;
            $gradeboundaryname = 'gradeboundary'.$i;

            if (property_exists($data, $gradeboundaryname) and $data->$gradeboundaryname != -1) {
                $letter = trim($data->$gradelettername);
                if ($letter == '') {
                    continue;
                }

                // <LSUGRADES> Hack to store non-integer decimal grade boundary data. Should this be done another way? I'm too lazy to figure it out. 
                $stored = $custom ? "{$data->$gradeboundaryname}" : $data->$gradeboundaryname;
                $letters[$stored] = $letter;
		// </LSUGRADES>

            }
        }
        krsort($letters, SORT_NUMERIC);

	// <LSUGRADES> Make sure letter grades and boundaries are set properly.
        $records = $DB->get_records('grade_letters', array('contextid' => $context->id), 'lowerboundary ASC', 'id');
	// </LSUGRADES>

        foreach($letters as $boundary=>$letter) {

	    // <LSUGRADES> Make sure letter grades and boundaries are set properly.
            $params = array(
                'letter' => $letter,
                'lowerboundary' => $boundary,
                'contextid' => $context->id
            );

            if ($record = $DB->get_record('grade_letters', $params)) {
                unset($records[$record->id]);
                continue;
	    // </LSUGRADES>

            } else {

	        // <LSUGRADES> Make sure letter grades and boundaries are set properly.
                $record = (object) $params;
	        // </LSUGRADES>

                $DB->insert_record('grade_letters', $record);
            }
        }

        // <LSUGRADES> Make sure letter grades and boundaries are set properly.
        $old_ids = array_keys($records);
	// </LSUGRADES>

        foreach($old_ids as $old_id) {
            $DB->delete_records('grade_letters', array('id' => $old_id));
        }

        redirect($returnurl);
    }

    print_grade_page_head($COURSE->id, 'letter', 'edit', get_string('editgradeletters', 'grades'));

    $mform->display();
}

echo $OUTPUT->footer();
