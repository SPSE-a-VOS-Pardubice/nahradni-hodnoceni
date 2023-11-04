package cz.spse.nahradnihodnoceni.repositories;

import cz.spse.nahradnihodnoceni.models.data.Exam;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.CrudRepository;
import org.springframework.data.repository.ListPagingAndSortingRepository;
import org.springframework.data.repository.query.QueryByExampleExecutor;

import java.util.List;

public interface ExamRepository extends CrudRepository<Exam, Long>, ListPagingAndSortingRepository<Exam, Long>, QueryByExampleExecutor<Exam> {
    @Query(value = "SELECT * FROM exam WHERE exam.year = ? AND exam.period = ?", nativeQuery = true)
    List<Exam> getForPeriod(int year, int period);

    @Query(value = "SELECT * FROM exam ORDER BY exam.year ASC, exam.period ASC LIMIT 1", nativeQuery = true)
    Exam getOldestExam();

    @Query(value = "SELECT * FROM exam ORDER BY exam.year DESC, exam.period DESC LIMIT 1", nativeQuery = true)
    Exam getLatestExam();
}
