package cz.spse.nahradnihodnoceni.repositories;

import cz.spse.nahradnihodnoceni.models.data.Teacher;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.CrudRepository;

public interface TeacherRepository extends CrudRepository<Teacher, Long> {
    @Query("SELECT t FROM Teacher t WHERE t.available AND NOT t.archived AND t.name = ?1 AND t.surname = ?2")
    Teacher findActiveByNameSurname(String name, String surname);
}
