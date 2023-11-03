package cz.spse.nahradnihodnoceni.repositories;

import cz.spse.nahradnihodnoceni.models.data.Exam;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.CrudRepository;
import org.springframework.data.repository.ListPagingAndSortingRepository;
import org.springframework.data.repository.query.QueryByExampleExecutor;

import java.util.List;

public interface ExamRepository extends CrudRepository<Exam, Long>, ListPagingAndSortingRepository<Exam, Long>, QueryByExampleExecutor<Exam> {
    @Query(value = "SELECT * FROM exam WHERE MONTH(exam.time) BETWEEN 1 AND 5 AND YEAR(exam.time) = ?", nativeQuery = true)
    List<Exam> getForFirstPeriod(int year);

    @Query(value = "SELECT * FROM exam WHERE MONTH(exam.time) BETWEEN 6 AND 9 AND YEAR(exam.time) = ?", nativeQuery = true)
    List<Exam> getForSecondPeriod(int year);

    @Query(value = "SELECT YEAR(exam.time) FROM exam WHERE exam.time IS NOT NULL ORDER BY exam.time ASC LIMIT 1", nativeQuery = true)
    Integer getOldestExamYear();
}
