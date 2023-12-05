package cz.spse.nahradnihodnoceni.repositories;

import cz.spse.nahradnihodnoceni.models.data.Classroom;
import cz.spse.nahradnihodnoceni.models.data.Exam;
import cz.spse.nahradnihodnoceni.models.data.Teacher;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.CrudRepository;

import java.util.List;

public interface ClassroomRepository extends CrudRepository<Classroom, Long> {
    @Query(value = "SELECT cr FROM Classroom cr WHERE cr.available AND NOT cr.archived AND cr.label = ?1")
    Classroom findActiveByLabel(String label);
}
