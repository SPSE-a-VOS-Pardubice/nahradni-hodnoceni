package cz.spse.nahradnihodnoceni.repositories;

import cz.spse.nahradnihodnoceni.models.data.Student;
import org.springframework.data.repository.CrudRepository;

public interface StudentRepository extends CrudRepository<Student, Long> {}