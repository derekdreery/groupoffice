<?php
// $Id: latex.php,v 1.1 2004/01/12 22:14:05 comsubvie Exp $

//
//  2002/03/18   Troy D. Straszheim  <troy@resophonic.com>
//

function template_view($page, $latex)
{
  global $WikiName, $HomePage, $WikiLogo, $MetaKeywords, $MetaDescription;
  global $FindScript, $pagestore;
?>
\documentclass{article}
%
% Latex generating code contributed to the wiki project by 
% Troy D. Straszheim <troy@resophonic.com>
%
%
% The latex at the top and at the very bottom of this file come from
% template/latex.php verbatim.
%

%
% uncomment these selectively to use various latex packages (fancyhdr,
% graphicx) and/or to add an EPS graphic of your choosing
%
%\usepackage{fancyhdr}
%\pagestyle{fancy}

%\usepackage{graphicx}
%\lhead{\large{\texttt{<?php print $HomePage . ":". $page; ?>}}}
% \rhead{\resizebox{2.5in}{!}{\includegraphics[trim=0 10 160 0, clip]{cb.eps}}}
%
%\chead{}
%\lfoot{\today}
%\cfoot{}
%\rfoot{}
%\renewcommand{\headrulewidth}{0.4pt}
%\renewcommand{\footrulewidth}{0.4pt}

\setlength{\parskip}{0.25cm}
\begin{document}

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
% here starts the latex generated by parse/latex.php %
% and action/latex.php				     %
%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

<?php print $latex; ?>

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
% here resumes template/latex.php generated latex.   %
%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

\end{document}

<?php
}
