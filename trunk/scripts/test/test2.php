<?php
require('../../www/Group-Office.php');

$mime ='Return-Path: <rvdmeer@houtwerf.nl>
Delivered-To: info@intermesh.nl
Received: by imfoss.nl (Postfix, from userid 5001)
	id 170A5238105; Mon, 25 Oct 2010 07:31:47 +0200 (CEST)
Received: from smx6.interconnect.nl (smx6.interconnect.nl [212.83.193.29])
	by imfoss.nl (Postfix) with ESMTP id 6A23B238104
	for <info@intermesh.nl>; Mon, 25 Oct 2010 07:31:47 +0200 (CEST)
Received: from mail.group-office.eu (HELO houtwerf.group-office.eu) ([213.207.89.149])
  by smtp.interconnect.nl with ESMTP; 25 Oct 2010 07:31:46 +0200
Message-ID: <1287984706.4cc516429cbdb@houtwerf.group-office.eu>
Date: Mon, 25 Oct 2010 07:31:46 +0200
Subject: trage werking group-office
From: Ron vd Meer <rvdmeer@houtwerf.nl>
To: Merijn Schering <info@intermesh.nl>, Jeffrey Blijleven
 <j.blijleven@houtwerf.nl>
MIME-Version: 1.0
Content-Type: multipart/mixed;
 boundary="_=_swift_v4_12879847064cc51642a6811_=_"
X-MimeOLE: Produced by Group-Office 3.5.37
X-Mailer: Group-Office 3.5.37
X-Priority: 3 (Normal)


--_=_swift_v4_12879847064cc51642a6811_=_
Content-Type: multipart/alternative;
 boundary="_=_swift_v4_12879847074cc51643055ef_=_"


--_=_swift_v4_12879847074cc51643055ef_=_
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: quoted-printable


=E2=80=8BMerijn,

zie bijlage

Ron



--_=_swift_v4_12879847074cc51643055ef_=_
Content-Type: text/html; charset=utf-8
Content-Transfer-Encoding: quoted-printable

<font face=3D"arial"><br>=E2=80=8BMerijn,<br><br>zie bijlage<br><br>Ron<br=
><br></font>

--_=_swift_v4_12879847074cc51643055ef_=_--

--_=_swift_v4_12879847064cc51642a6811_=_--
';


global $GO_CONFIG,$GO_MODULES,$GO_SECURITY;
require_once($GO_CONFIG->class_path.'mail/GoSwift.class.inc.php');
require_once($GO_MODULES->modules['email']['class_path']."email.class.inc.php");
require_once($GO_MODULES->modules['sync']['class_path']."settings.class.inc.php");
$email = new email();
$goswift = new GoSwiftImport($mime,true);

echo $goswift->body;



