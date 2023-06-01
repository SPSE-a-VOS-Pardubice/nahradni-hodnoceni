package cz.spse.nahradnihodnoceni.repositories;

import java.util.List;

import org.springframework.data.repository.ListPagingAndSortingRepository;

import cz.spse.nahradnihodnoceni.models.data.Exam;
import org.springframework.data.repository.CrudRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.data.repository.query.QueryByExampleExecutor;
import org.springframework.data.domain.Pageable;

public interface ExamRepository extends CrudRepository<Exam, Long>, ListPagingAndSortingRepository<Exam, Long>, QueryByExampleExecutor<Exam> {

    @Query("SELECT e FROM Exam e WHERE (:status IS NULL OR (:status IS NOT NULL AND (e.finalMark IS NOT NULL AND :status = 0 OR e.finalMark IS NULL AND :status = 1))) AND (:type IS NULL OR (:type IS NOT NULL AND (e.originalMark = 'N' AND :type = 0 OR e.originalMark <> 'N' AND :type = 1))) AND (:successful IS NULL OR (:successful IS NOT NULL AND (e.finalMark <> '5' AND :successful = 0 OR e.finalMark = '5' AND :successful = 1))) AND (:mark IS NULL OR :mark = e.finalMark)")
    List<Exam> findByStatusTypeSuccessfulMark(@Param("status") Integer status, @Param("type") Integer type, @Param("successful") Integer successful, @Param("mark") Integer mark, Pageable pageable);
}
