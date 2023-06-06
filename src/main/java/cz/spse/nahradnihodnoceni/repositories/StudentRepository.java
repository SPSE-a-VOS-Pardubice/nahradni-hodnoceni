package cz.spse.nahradnihodnoceni.repositories;

import cz.spse.nahradnihodnoceni.models.data.Student;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.CrudRepository;

public interface StudentRepository extends CrudRepository<Student, Long> {
    @Query("SELECT s FROM Student s WHERE s.name = ?1 AND s.surname = ?2 AND s._class.year = ?3 AND s._class.label = ?4")
    Student findByNameAndClass(String studentName, String studentSurname, int year, String classLabel);
}
