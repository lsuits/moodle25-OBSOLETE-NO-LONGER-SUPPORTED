Moodle 2.5 with the LSU Gradebook
======

Additional features included in this release
--

Gradebook
-
1. Extra Credit in Weighted Mean.
1. Repeat headers every N rows in grader report.
2. Optional anonymous grading allows faculty to manually grade items in an anonymous manner.
2. Quick Edit allows for faculty to edit a single item or student quickly and easilty while allowing for bulk grading.
3. Gradebook Builder is for those who fear the complexity of the Moodle gradebook. Think of it as a quick-start.
4. Optional enhanced letter grade support.
5. Optional Curve-to instead of multiplicator for a simpler to understand method of curving.
6. Optional Manual Item Recompute allows for the gradebook to automatically adjust the raw grade boundaries when editing the real grade boundaries for Manual items.
7. Optional hiding of minimum grade.
8. Optional ability to disable editing of the course category name.
9. Option to count extra credit in weighted categories in a weighted or unweighted manner.
10. Optional course category lockout. Stop faculty from overriding course category totals.
11. Optional drop the lowest grade limiting allows drop lowest to only drop lowest X when grade count is X+1. This gives students a better representation of their final grade as the course progresses.

Weighted Mean Extra Credit
-
1. Simple as it sounds. Now you can add extra credit items to Weighted Mean categories.

Repeat Headers
-
1. Users can choose to repeat column headers that show category and item information every N rows in the grader report. This limits the need for complex javascript solutions and makes the gradebook much more usable.

Anonymous Grading
-
1. Choose course categories of a single category to limit its use.
2. Unlimited grading is disabled for anonymously graded courses.
3. Administratively chosen (course overrideable) adjustment values after student names are learned for course participation assignment.
4. Anonymous items can only be edited while students are unknown,
5. Utilizes additional profile fields. Please provide a private field for this functionality.

Quick Edit
-
1. Choose to edit all grades for a single user.
2. Choose to edit all student grades for a single item.
3. Required for editing anonymous grades.
4. Allows for bulk grading. Insert X for empty students or insert X for all students.
5. Quickly override all gades or no grades.

Available from https://github.com/lsuits/quick_edit

Gradebook Builder
-
1. Create manual items or quiz shells in a simple streamlined interface.
2. Allows for 3rd party plugins for all grade Moodle course modules.
3. Quickly build very complex gradebooks.
4. Ensures weights make sense.
5. Re-use gradebook templates.

Available from https://github.com/lsuits/gradebook_builder


Letters
-
1. Optional fixed letters for schools that require specific letter grades.
2. Optional floating letter boundaries.
3. Letter grade rounding adheres to instructor decimal preferences if set.

Grader Report
-
1. Optional Quick Edit integration.
2. Optional weighted percents show an items weight relative to its parent category.
3. Optional Repeat Headers allows for faculty to repeat the grader report headers every x rows for easy foolproof navigation and grading.
