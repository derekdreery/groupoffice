<?php
// holidays with fixed date
$input_holidays['fix']['01-01'] = 'Nyårsdagen';
$input_holidays['fix']['01-06'] = 'Trettondedag jul';
$input_holidays['fix']['05-01'] = 'Första maj';
$input_holidays['fix']['06-06'] = 'Sveriges nationaldag';
$input_holidays['fix']['12-25'] = 'Juldagen';
$input_holidays['fix']['12-26'] = 'Annandag jul';

// holidays with variable date (christian holidays computation is based on the date of easter day)
$input_holidays['var']['-2'] = 'Långfredagen';
$input_holidays['var']['0'] = 'Påskdagen';
$input_holidays['var']['1'] = 'Annandag påsk';
$input_holidays['var']['39'] = 'Kristi himmelsfärdsdag';
$input_holidays['var']['49'] = 'Pingstdagen';

// // Midsummers Day: the saturday between June 20 and 26
// $input_holidays['var']['sv']['**'] = 'Midsommardagen';
// // All hallows day: the saturday between October 31 and November 6
// $input_holidays['var']['sv']['**'] = 'Alla helgons dag';
