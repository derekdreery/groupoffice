


GO.backupmanager.lang.backupmanager=' ব্যাক-আপ ব্যবস্থাপক ';
GO.backupmanager.lang.rmachine=' দূরবর্তী মেসিন ';
GO.backupmanager.lang.rport=' পোর্ট ';
GO.backupmanager.lang.rtarget=' গন্তব্য ফোল্ডার ';
GO.backupmanager.lang.sources=' উৎস ফোল্ডারগুলো ';
GO.backupmanager.lang.rotations=' আবর্তনসমূহ ';
GO.backupmanager.lang.quiet=' স্থির ';
GO.backupmanager.lang.emailaddresses='ই-মেইল ঠিকানাগুলো ';
GO.backupmanager.lang.emailsubject=' ই-মেইলের বিষয় ';
GO.backupmanager.lang.rhomedir=' দূরবর্তী  homedir';
GO.backupmanager.lang.rpassword='কূটশব্দ ';
GO.backupmanager.lang.publish=' প্রকাশ কর ';
GO.backupmanager.lang.enablebackup=' ব্যাক-আপ শুরু কর ';
GO.backupmanager.lang.disablebackup=' ব্যাক-আপ শেষ কর ';
GO.backupmanager.lang.successdisabledbackup=' ব্যাক-আপ নিষ্ক্রিয়করণ সুসম্পন্ন হয়েছে ! ';
GO.backupmanager.lang.publishkey=' ব্যাক-আপ সক্রিয় কর ';
GO.backupmanager.lang.publishSuccess=' ব্যাক-আপ সক্রিয়করণ সুসম্পন্ন হয়েছে । ';
GO.backupmanager.lang.helpText=' এই মডিউলটি  SSH এবং  resync ব্যবহার করে  কোন একটি দূরবর্তী সার্ভারে সমস্ত ফাইল এবং MySQL ডাটাবেজের ব্যাক-আপ নেবে ( নিশ্চিত হোন যে  উৎস ফোল্ডারে   /home/mysqlbackup সংযুক্ত আছে )   যখন এটি সক্রিয় থাকবে তখন এটি সার্ভারে   SSH public key প্রকাশ করবে এবং পরখ করে দেখবে যে গন্তব্য সার্ভারটি  অস্তিত্বশীল কি না । তাই প্রথমেই  দূরবর্তী ব্যাক-আপ ফোল্ডারটি  অস্তিত্ব নিশ্চিত করুন ।পূর্বনির্ধারিত হিসাবে  /etc/cron.d/groupoffice-backup এ ব্যাক-আপের সময় মধ্যরাতে নির্ধারণ করা আছে। ওই ফাইলে আপনি শিডিউলটি পূনঃনির্ধারণ করে দিতে পারেন  অথবা ফাইলটি না থাকলে তৈরি করতে পারেন । বিকল্প হিসাবে আপনি টার্মিনালে "php /usr/share/groupoffice/modules/backupmanager/cron.php" নির্দেশটি নির্বাহ করে অযান্ত্রিকভাবে ব্যাক-আপ চালু করতে পারবেন ।';