��    �      �  �   �
      �  �   �  �   2  %   �  =     .   ?  %   n  �   �  �   ~  a   *  G   �  J   �  I     %  i  �   �  �   +  A   �  ;   $  <   `  5   �  ;   �  G     <   W  0   �  =   �  ;     <   ?  ;   |  <   �     �     �  �     �   �  7   8  7   p  /   �  ,   �  -        3     D  
   P     [     k     �     �     �     �     �          /      3     T     \     d     k     ~     �     �     �     �     �     �     �  &        2     :     C     J     S     h     o     �  #   �     �     �  %   �     �               8      Q  @   r  �   �     m     �     �     �     �  �   �     H      U      l      }      �      �   	   �   .   �      �      �      	!     !!  	   >!     H!     W!     f!  P   l!  Q   �!     "     "  6   "     L"  $   h"     �"     �"     �"     �"     �"     �"  Q   	#     [#     c#  %   �#  -   �#     �#     �#     �#      $  "   $     1$     I$     Q$     Y$  	   f$     p$  
   ~$     �$     �$     �$     �$  !   �$     �$     %     %     7%     I%  4   Z%     �%     �%  $   �%     �%     �%     �%     &     &     !&     ;&     X&     r&     �&     �&     �&     �&     �&     �&  %   '     ,'     3'     <'      L'     m'  �   �'     2(  *   J(  �  u(  v  *  J  �+  T   �,  t   (-  a   �-  p   �-  �  p.  �  @0  �   �1  �   �2  �   %3  �   �3  d  :4  <  �5  q  �6  {   N8  p   �8  }   ;9  E   �9  y   �9  �   y:  t   �:  @   t;  t   �;  e   *<  g   �<  a   �<  O   Z=     �=  J   �=  %  >  ;  *?  ~   f@  �   �@  m   jA  e   �A  e   >B  B   �B  6   �B  6   C  A   UC  U   �C  U   �C  L   CD  8   �D  1   �D  P   �D  1   LE     ~E  (   �E     �E     �E     �E     �E  (   F     <F  /   SF  U   �F     �F  
   �F  E    G  e   FG  Y   �G     H     H     7H     HH  3   dH     �H  2   �H     �H  O    I  2   PI     �I  E   �I  "   �I  0   J  @   5J  7   vJ  G   �J  �   �J  �  �K  ,   8M  1   eM  8   �M     �M     �M  �  �M  $   }O  -   �O  8   �O     	P     P  3   'P     [P  Y   uP  2   �P  P   Q  /   SQ  A   �Q     �Q  5   �Q  +   R     :R  �   JR  �   �R  
   �S  
   �S  l   �S  J   T  Q   dT     �T     �T  '   �T  S   U  -   WU  9   �U  �   �U     �V  X   �V  L   �V  l   9W     �W  '   �W  ,   �W     X  K   .X  3   zX     �X     �X     �X     �X     �X     Y     +Y  .   8Y     gY  B   �Y  K   �Y     Z     5Z  6   KZ  %   �Z  %   �Z  n   �Z     =[  R   F[  F   �[     �[  I   �[  6   <\  
   s\     ~\  D   �\  I   �\  /   ']  3   W]  -   �]  9   �]  0   �]     $^     B^  S   S^  D   �^     �^     �^  "   _  T   @_     �_  F  �_  F   �`  S   >a            E   �   �   �       �   Q   $          A       B   R          �   #   i   '   �           �          s      I      �       n   v   `          �       ~   �       	   M   �   q   �   �   �   �      @                 �   r   =   a   �   o   1   �   0       f   d   ^       z   �          (   
   ,       �       {              U   9   �   h       ]   &           .   �   �   [   S   C   �   ;   K   4   w       D   �   �   V       �       8      y   �   �       6       �   �   c   u   |   _          t   �   W   Z       X       T          !   x   p       �   N   b   �          2       5              �   �   �                 �   /   \   Y   �       }   O       J   l      )   ?           +       �         >   3   �   7   %   m   <           �           j                 �   e       �   H   g           F   k   P              G       �      :   -   �   *      "              L               ->  It will ban particular users by just putting their ids seprated by commas(,). If user is Ban then they will not able to access wp file manager on front end. -> * for all operations and to allow some operation you can mention operation name as like, allowed_operations="upload,download". Note: seprated by comma(,). Default: * -> File Manager Theme. Default: Light -> File Modified or Create date format. Default: d M, Y h:i A -> File manager Language. Default: English(en) -> Filemanager UI View. Default: grid -> Here "test" is the name of folder which is located on root directory, or you can give path for sub folders as like "wp-content/plugins". If leave blank or empty it will access all folders on root directory. Default: Root directory -> It will allow all roles to access file manager on front end or You can simple use for particular user roles as like allowed_roles="editor,author" (seprated by comma(,)) -> It will lock mentioned in commas. you can lock more as like ".php,.css,.js" etc. Default: Null -> for access to read files permission, note: true/false, default: true -> for access to write files permissions, note: true/false, default: false -> it will hide mentioned here. Note: seprated by comma(,). Default: Null <code>[wp_file_manager view="list" lang="en" theme="light" dateformat="d M, Y h:i A" allowed_roles="editor,author" access_folder="wp-content/plugins" write = "true" read = "false" hide_files = "kumar,abc.php" lock_extensions=".php,.css" allowed_operations="upload,download" ban_user_ids="2,3"] <code>[wp_file_manager]</code> -> It will show file manager on front end. But only Administrator can access it and will control from file manager settings. <code>[wp_file_manager_admin]</code> -> It will show file manager on front end. You can control all settings from file manager settings. It will work same as backend WP File Manager. <span class="fm_console_error">Nothing selected for backup</span> <span class="fm_console_error">Others backup failed.</span> <span class="fm_console_error">Plugins backup failed.</span> <span class="fm_console_error">Security Issue.</span> <span class="fm_console_error">Themes backup failed.</span> <span class="fm_console_error">Unable to create database backup.</span> <span class="fm_console_error">Uploads backup failed.</span> <span class="fm_console_success">All Done</span> <span class="fm_console_success">Database backup done.</span> <span class="fm_console_success">Others backup done.</span> <span class="fm_console_success">Plugins backup done.</span> <span class="fm_console_success">Themes backup done.</span> <span class="fm_console_success">Uploads backup done.</span> Action Actions upon selected backup(s) Admin can restrict actions of any user. Also hide files and folders and can set different - different folders paths for different users. Admin can restrict actions of any userrole. Also hide files and folders and can set different - different folders paths for different users roles. After enable trash, your files will go to trash folder. After enabling this all files will go to media library. Are you sure want to remove selected backup(s)? Are you sure you want to delete this backup? Are you sure you want to restore this backup? Backup / Restore Backup Date Backup Now Backup Options: Backup data (click to download) Backup files will be under Backup is running, please wait Backup not found! Backup removed successfully! Backup successfully deleted. Backups removed successfully! Ban Browser and OS (HTTP_USER_AGENT) Buy PRO Buy Pro Cancel Change Theme Here: Code-editor View Confirm Copy files or folders Currently no backup(s) found. DELETE FILES Dark Database Backup Database backup done on date  Database backup restored successfully. Default Default: Delete Deselect Dismiss this notice. Donate Download Files Logs Download files Duplicate or clone a folder or file Edit Files Logs Edit a file Enable Files Upload to Media Library? Enable Trash? Existing Backup(s) Extract archive or zipped file File Manager - Shortcode File Manager - System Properties File Manager Root Path, you can change according to your choice. File Manager has a code editor with multiple themes. You can select any theme for code editor. It will display when you edit any file. Also you can allow fullscreen mode of code editor. File Operations List: File doesn't exist to download. Files Backup Gray Help Here admin can give access to user roles to use filemanager. Admin can set Default Access Folder and also control upload size of filemanager. Info of file Invalid Security Code. Last Log Message Light Logs Make directory or folder Make file Maximum file upload size (upload_max_filesize) Memory Limit (memory_limit) Missing backup id. Missing parameter type. Missing required parameters. No Thanks No log message No logs found! Note: Note: These are demo screenshots. Please buy File Manager pro to Logs functions. Note: This is just a demo screenshot. To get settings please buy our pro version. OK Ok Others (Any other directories found inside wp-content) Others backup done on date  Others backup restored successfully. PHP version Parameters: Paste a file or folder Please Enter Email Address. Please Enter First Name. Please Enter Last Name. Please change this carefully, wrong path can lead file manager plugin to go down. Plugins Plugins backup done on date  Plugins backup restored successfully. Post maximum file upload size (post_max_size) Preferences Privacy Policy Public Root Path RESTORE FILES Remove or delete files and folders Rename a file or folder Restore SUCCESS Save Changes Saving... Search things Select All Settings Settings - Code-editor Settings - General Settings - User Restrictions Settings - User Role Restrictions Settings saved. Shortcode - PRO Simple cut a file or folder System Properties Terms of Service The backup apparently succeeded and is now complete. Themes Themes backup done on date  Themes backup restored successfully. Time now Timeout (max_execution_time) To make a archive or zip Today USE: Unable to removed backup! Unable to restore DB backup. Unable to restore others. Unable to restore plugins. Unable to restore themes. Unable to restore uploads. Upload Files Logs Upload files Uploads Uploads backup done on date  Uploads backup restored successfully. Verify View Log WP File Manager WP File Manager - Backup/Restore WP File Manager Contribution We love making new friends! Subscribe below and we promise to
    keep you up-to-date with our latest new plugins, updates,
    awesome deals and a few special offers. Welcome to File Manager You have not made any changes to be saved. Project-Id-Version: WP File Manager
Report-Msgid-Bugs-To: 
PO-Revision-Date: 2021-07-16 14:28+0530
Last-Translator: admin <kajal.gill@mysenseinc.in>
Language-Team: 
Language: be
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=2; plural=n != 1;
X-Generator: Poedit 2.4.3
X-Poedit-KeywordsList: __;_e
X-Poedit-Basepath: ..
X-Poedit-SearchPath-0: .
 -> Гэта забароніць пэўных карыстальнікаў, проста паставіўшы іх ідэнтыфікатары, падзеленыя коскамі (,). Калі карыстальнік забаронены, ён не зможа атрымаць доступ да файлавага мэнэджара wp на пярэдняй панэлі. -> * для ўсіх аперацый і, каб дазволіць нейкую аперацыю, вы можаце назваць назву аперацыі як, напрыклад, allowed_operations = "загрузіць, загрузіць". Заўвага: раздзелена коскай (,). Па змаўчанні: * -> Тэма дыспетчара файлаў. Па змаўчанні: святло -> Зменены файл альбо Стварыць фармат даты. Па змаўчанні: d M, Y h: i A -> Мова дыспетчара файлаў. Па змаўчанні: англійская (en) -> Прагляд карыстацкага інтэрфейсу Filemanager. Па змаўчанні: сетка -> Тут "тэст" - гэта назва тэчкі, якая знаходзіцца ў каранёвым каталогу, альбом можна адказаць шляхам для падтэксту як "wp-content / plugins". Калі будзе паля пустым альбомам пустым, ён атрымае доступ да ўсіх тэчак у каранёвым каталогу. Па змаўчанні: Каранёвы каталог -> Гэта дазволіць усім ролям атрымаць доступ да файлавага мэнэджара на пярэдняй панэлі альбо вы можаце проста выкарыстоўваць яго для пэўных роляў карыстальнікаў, напрыклад, allowed_roles = "рэдактар, аўтар" (раздзелена коскай (,)) -> Ён будзе заблакаваны, згаданыя ў косках. вы можаце заблакаваць больш як ".php, .css, .js" і г. д. Па змаўчанні: Нуль -> для доступу да дазволу на чытанне файлаў, заўвага: true / false, па змаўчанні: true -> для доступу да дазволаў на запіс файлаў, заўвага: true / false, па змаўчанні: false -> гэта схавае згаданае тут. Заўвага: раздзелена коскай (,). Па змаўчанні: нуль <code> [wp_file_manager view = "list" lang = "en" theme = "light" dateformat = "d M, Y h: i A" allowed_roles = "рэдактар, аўтар" access_folder = "wp-content / plugins" write = "true" read = "false" hide_files = "kumar, abc.php" lock_extensions = ". php, .css" allowed_operations = "загрузіць, загрузіць" ban_user_ids = "2,3"] <code> [wp_file_manager] </code> -> Ён будзе паказваць файлавы менеджэр на пярэдняй панэлі. Але толькі Адміністратар можа атрымаць да яго доступ і будзе кіраваць з налад дыспетчара файлаў. <code> [wp_file_manager_admin] </code> -> Ён будзе паказваць файлавы менеджэр на пярэдняй панэлі. Вы можаце кіраваць усімі наладамі з налад дыспетчара файлаў. Ён будзе працаваць гэтак жа, як і серверны дыспетчар файлаў WP. <span class="fm_console_error">Для рэзервовага капіравання нічога не выбрана</span> <span class="fm_console_error">Не атрымалася зрабіць рэзервовую копію. </span> <span class="fm_console_error">Не атрымалася зрабіць рэзервовую копію убудоў. </span> <span class="fm_console_error">Пытанне бяспекі. </span> <span class="fm_console_error">Не атрымалася стварыць рэзервовую копію тэм. </span> <span class="fm_console_error">Немагчыма стварыць рэзервовую копію базы дадзеных. </span> <span class="fm_console_error">Не атрымалася загрузіць рэзервовую копію. </span> <span class="fm_console_success">Усе зроблена </span> <span class="fm_console_success">Зроблена рэзервовая копія базы дадзеных. </span> <span class="fm_console_success">Іншыя рэзервовыя копіі зроблены. </span> <span class="fm_console_success">Зроблена рэзервовая копія убудоў. </span> <span class="fm_console_success">Зроблена рэзервовая копія тэм. </span> <span class="fm_console_success">Запампоўка зроблена. </span> Дзеянне Дзеянні з выбранымі рэзервовымі копіямі Адміністратар можа абмежаваць дзеянні любога карыстальніка. Акрамя таго, можна схаваць файлы і тэчкі і можа ўсталяваць розныя шляхі для розных карыстальнікаў. Адміністратар можа абмежаваць дзеянні любой карыстальніцкай ролі. Акрамя таго, можна схаваць файлы і тэчкі і можа ўсталяваць розныя шляхі для розных роляў карыстальнікаў. Пасля ўключэння смецця вашы файлы будуць пераходзіць у папку смецця. Пасля ўключэння гэтага ўсе файлы будуць пераходзіць у медыя-бібліятэку. Вы ўпэўнены, што хочаце выдаліць выбраныя рэзервовыя копіі? Вы ўпэўнены, што хочаце выдаліць гэту рэзервовую копію? Вы ўпэўнены, што хочаце аднавіць гэту рэзервовую копію? Рэзервовае капіраванне / аднаўленне Дата рэзервовага капіравання Рэзервовае капіраванне зараз Параметры рэзервовага капіравання: Рэзервовыя дадзеныя (націсніце, каб загрузіць) Файлы рэзервовых копій будуць знаходзіцца пад Рэзервовае капіраванне працуе, пачакайце Рэзервовая копія не знойдзена! Рэзервовая копія выдалена! Рэзервовае капіраванне паспяхова выдалена. Рэзервовыя копіі выдалены! Забараніць Браўзэр і АС (HTTP_USER_AGENT) купіць PRO Купляйце Pro Адмяніць Змяніць тэму тут: Выгляд рэдактара кода Пацвердзіце Скапіруйце файлы ці тэчкі У цяперашні час рэзервовых копій не знойдзена. ВЫДАЛІЦЬ ФАЙЛЫ Цёмны Рэзервовае капіраванне базы дадзеных Рэзервовае капіраванне базы дадзеных зроблена на дату  Рэзервовае капіраванне базы дадзеных адноўлена. Па змаўчанні Па змаўчанні: Выдаліць Адмяніць выбар Адхіліць гэтае апавяшчэнне. ахвяраваць Спампаваць часопісы файлаў Спампаваць файлы Дублюйце альбо кланіруйце тэчку альбо файл Рэдагаваць часопісы файлаў Змяніць файл Уключыць загрузку файлаў у медыятэку? Уключыць сметніцу? Існуючыя рэзервовыя копіі Выняць архіў альбо архіваваны файл Файлавы менеджэр - кароткі код Дыспетчар файлаў - Уласцівасці сістэмы Каранёвы шлях файлавага мэнэджара, вы можаце змяніць яго ў адпаведнасці з вашым выбарам. Файлавы менеджэр мае рэдактар ​​кода з некалькімі тэмамі. Вы можаце выбраць любую тэму для рэдактара кода. Ён будзе адлюстроўвацца пры рэдагаванні любога файла. Таксама вы можаце дазволіць поўнаэкранны рэжым рэдактара кода. Спіс аперацый з файламі: Файл не існуе для загрузкі. Рэзервовае капіраванне файлаў Шэры Дапамажыце Тут адміністратар можа даць доступ да роляў карыстальнікаў для выкарыстання файлавага мэнэджэра. Адміністратар можа ўсталяваць папку доступу па змаўчанні, а таксама кантраляваць памер загрузкі файлавага менеджэра. Інфармацыя пра файл Несапраўдны код бяспекі. Апошняе паведамленне часопіса Святло Часопісы Зрабіце каталог альбо тэчку Стварыць файл Максімальны памер загружанага файла (upload_max_filesize) Абмежаванне памяці (memory_limit) Адсутнічае ідэнтыфікатар рэзервовай копіі. Адсутнічае тып параметра. Адсутнічаюць неабходныя параметры. Не, дзякуй Няма паведамлення ў часопісе Часопісаў не знойдзена! нататка: Заўвага: Гэта дэманстрацыйныя скрыншоты. Калі ласка, купіце File Manager pro для функцый часопісаў. Заўвага: Гэта проста дэманстрацыйны скрыншот. Каб атрымаць налады, купіце нашу версію pro. добра Добра Іншыя (любыя іншыя каталогі, якія знаходзяцца ўнутры wp-content) Іншыя рэзервовыя копіі зроблены на дату  Іншыя рэзервовыя копіі паспяхова адноўлены. Версія PHP Параметры: Устаўце файл ці тэчку Калі ласка, увядзіце адрас электроннай пошты. Калі ласка, увядзіце імя. Калі ласка, увядзіце прозвішча. Калі ласка, змяніце гэта ўважліва, няправільны шлях можа прывесці да падзення ўбудовы файлавага мэнэджара. Убудовы Рэзервовае капіраванне убудоў зроблена на дату  Рэзервовае капіраванне убудоў адноўлена. Апублікаваць максімальны памер загружанага файла (post_max_size) Прэферэнцыі Палітыка прыватнасці Грамадскі каранёвы шлях АДНАВІЦЬ ФАЙЛЫ Выдаленне альбо выдаленне файлаў і тэчак Перайменаваць файл ці тэчку Аднавіць ПОСПЕХ Захаваць змены Захаванне ... Шукайце рэчы Абраць усё налады Налады - рэдактар ​​кода Налады - Агульныя Налады - Абмежаванні карыстальнікаў Налады - Абмежаванні роляў карыстальніка Налады захаваны. Шорт-код - PRO Проста выражыце файл ці тэчку ўласцівасці сістэмы Ўмовы абслугоўвання Рэзервовае капіраванне, відаць, атрымалася, і яно завершана. Тэмы Рэзервовае капіраванне тэм зроблена на дату  Рэзервовае капіраванне тэм адноўлена. Час зараз Час чакання (максімальны_ час_выканання) Каб зрабіць архіў альбо архіў Сёння ВЫКАРЫСТАННЕ: Немагчыма выдаліць рэзервовую копію! Немагчыма аднавіць рэзервовую копію БД. Немагчыма аднавіць іншыя. Немагчыма аднавіць убудовы. Немагчыма аднавіць тэмы. Немагчыма аднавіць запампоўкі. Загрузіць часопісы файлаў Загрузіць файлы Загружае Загружае рэзервовую копію, зробленую на дату  Загрузка рэзервовай копіі адноўлена. Праверце Прагляд часопіса Дыспетчар файлаў WP WP File Manager - Рэзервовае капіраванне / аднаўленне Уклад WP File Manager Мы любім знаходзіць новых сяброў! Падпішыцеся ніжэй, і мы абяцаем
    будзеце ў курсе нашых апошніх новых убудоў, абнаўленняў,
    дзіўныя прапановы і некалькі спецыяльных прапаноў. Сардэчна запрашаем у дыспетчар файлаў Вы не ўносілі ніякіх змяненняў для захавання. 