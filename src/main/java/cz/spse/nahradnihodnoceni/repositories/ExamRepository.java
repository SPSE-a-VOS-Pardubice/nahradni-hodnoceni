package cz.spse.nahradnihodnoceni.repositories;

import cz.spse.nahradnihodnoceni.models.data.Exam;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.CrudRepository;
import org.springframework.data.repository.ListPagingAndSortingRepository;
import org.springframework.data.repository.query.QueryByExampleExecutor;

import java.util.List;

public interface ExamRepository extends CrudRepository<Exam, Long>, ListPagingAndSortingRepository<Exam, Long>, QueryByExampleExecutor<Exam> {
    @Query("SELECT e FROM Exam e WHERE e.originalMark = 'N'")
    List<Exam> findAllNH();

    @Query("SELECT e FROM Exam e WHERE e.originalMark = '5'")
    List<Exam> findAllOZ();

    @Query("SELECT e FROM Exam e WHERE e.originalMark = 'N' AND e.finalMark IS NOT NULL")
    List<Exam> findAllFinishedNH();

    @Query("SELECT e FROM Exam e WHERE e.originalMark = '5' AND e.finalMark IS NOT NULL")
    List<Exam> findAllFinishedOZ();

    @Query("SELECT e FROM Exam e WHERE e.finalMark in ('1', '2', '3', '4')")
    List<Exam> findAllSuccesedExams();

    @Query("SELECT e FROM Exam e WHERE e.finalMark in ('5', 'N')")
    List<Exam> findAllFailedExams();

    @Query("SELECT e FROM Exam e WHERE e.finalMark IS NULL")
    List<Exam> findAllUnmarkedExams();
}

