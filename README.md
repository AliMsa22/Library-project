Empowering Your Academic Journey
IUL-CourseFlow, an innovative platform designed to streamline and enhance the management of academic activities within a university. The system provides a comprehensive solution for managing courses, faculties, and student information, fostering an efficient academic environment.

=> Steps:

      1.Download all files and set them into "C:\xampp\htdocs\library\" directory .
      
      2.Create in your sql new database "library" then import "LibraryDataBase" sql file from includes.
      
      3.Create into "assets" directory the "adminUploads" folder, and into the "assets\img" create two folder "major" & "faculty".
      
      4.Open the web locally with "http://localhost/library/index.php" url.
      
      5.Admin login : email- ali@gmail.com
                      pass - amst/123
                      
            Can add/drop faculty
                add/drop majors
                add/delete/edit subject (can be a common subject with other majors)
                add/delete/edit/download subject materials (Add specific file type, < 5MB)
                manage student accounts (Block/Unblock)
                edit account info
                
      6.Student login : email -  amst@gmail.com
                        pass - amst/123
                        
            Can download subject materials
                edit account info
                
      7.Student signup : system validate email used and check existence
                         system check password format

      All error/success is handled with alert/message to indicate status for users.
