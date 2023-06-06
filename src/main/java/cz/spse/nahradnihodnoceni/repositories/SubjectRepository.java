package cz.spse.nahradnihodnoceni.repositories;

import cz.spse.nahradnihodnoceni.models.data.Subject;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.CrudRepository;

public interface SubjectRepository extends CrudRepository<Subject, Long> {
    @Query("SELECT s FROM Subject s WHERE s.abbreviation = ?1")
    Subject findByAbbreviation(String abbreviation);
}
