package cz.spse.nahradnihodnoceni.repositories;

import cz.spse.nahradnihodnoceni.models.data.Exam;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.CrudRepository;
import org.springframework.data.repository.ListPagingAndSortingRepository;
import org.springframework.data.repository.query.Param;
import org.springframework.data.repository.query.QueryByExampleExecutor;

import java.util.List;

public interface ExamRepository extends CrudRepository<Exam, Long>, ListPagingAndSortingRepository<Exam, Long>, QueryByExampleExecutor<Exam> {

    @Query("SELECT e FROM Exam e WHERE " +
            "(:status IS NULL OR (:status IS NOT NULL AND (e.finalMark IS NOT NULL AND :status = 0 OR e.finalMark IS NULL AND :status = 1))) " +
        "AND (:type IS NULL OR (:type IS NOT NULL AND (e.originalMark = 'N' AND :type = 0 OR e.originalMark <> 'N' AND :type = 1))) " +
        "AND (:successful IS NULL OR (:successful IS NOT NULL AND (e.finalMark <> '5' AND :successful = 0 OR e.finalMark = '5' AND :successful = 1)))" +
        "AND (:mark IS NULL OR :mark = e.finalMark)")
    List<Exam> findByStatusTypeSuccessfulMark(@Param("status") Integer status, @Param("type") Integer type, @Param("successful") Integer successful, @Param("mark") Integer mark, Pageable pageable);

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
