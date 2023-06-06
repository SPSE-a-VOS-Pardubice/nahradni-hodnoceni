package cz.spse.nahradnihodnoceni.repositories;

import cz.spse.nahradnihodnoceni.models.data._Class;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.CrudRepository;

public interface _ClassRepository extends CrudRepository<_Class, Long> {
    @Query("SELECT c FROM _Class c WHERE c.year = ?1 AND c.label = ?2")
    _Class find(int year, String classLabel);
}
