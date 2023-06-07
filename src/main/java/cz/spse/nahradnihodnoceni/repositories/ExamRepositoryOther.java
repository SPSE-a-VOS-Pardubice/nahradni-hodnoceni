package cz.spse.nahradnihodnoceni.repositories;

import cz.spse.nahradnihodnoceni.models.data.Exam;
import jakarta.persistence.EntityManager;
import jakarta.persistence.PersistenceContext;
import jakarta.persistence.criteria.Predicate;
import org.springframework.lang.Nullable;
import org.springframework.stereotype.Repository;

import java.util.List;

@Repository
public class ExamRepositoryOther {
    @PersistenceContext
    private EntityManager em;

    public List<Exam> findFiltered(@Nullable FILTER_STATUS status, @Nullable FILTER_TYPE type, @Nullable FILTER_SUCCESS success, @Nullable String mark, @Nullable SORT_BY sortBy, @Nullable Boolean reversed, String text) {
        var qb = em.getCriteriaBuilder();
        var query = qb.createQuery(Exam.class);
        var root = query.from(Exam.class);
        query.select(root);

        if (status != null) {
            switch (status) {
                case FINISHED -> query.where(qb.isNotNull(root.get("finalMark")));
                case UNFINISHED -> query.where(qb.isNull(root.get("finalMark")));
            }
        }

        if (type != null) {
            switch (type) {
                case NAHRADNI_HODNOCENI -> query.where(qb.equal(root.get("originalMark"), "N"));
                case OPRAVNA_ZKOUSKA -> query.where(qb.equal(root.get("originalMark"), "5"));
            }
        }

        if (success != null) {
            switch (success) {
                case SUCCESSFUL -> query.where(qb.and(
                        qb.isNotNull(root.get("finalMark")),
                        qb.notEqual(root.get("finalMark"), "5")
                ));
                case FAILED -> query.where(qb.or(
                        qb.isNull(root.get("finalMark")),
                        qb.equal(root.get("finalMark"), "5")
                ));
            }
        }

//        if (mark != null) {
//
//        }

        // TODO
        if (sortBy != null) {
            var model = switch (sortBy) {
                case STUDENT -> root.get("student").get("surname");
                case TEACHER -> root.get("examiner").get("surname");
                case CLASS -> root.get("student").get("_class").get("year");
                case MARK -> root.get("finalMark");
            };
            var order = reversed == Boolean.TRUE ? qb.desc(model) : qb.asc(model);
            query.orderBy(order);
        }

        if (!text.isEmpty()) {
            /**
             * Predicate eventNamePredicate = builder.like(root.get("eventName"), "%" + searchText + "%");
             * Predicate eventDescPredicate = builder.like(root.get("eventDescription"), "%" + searchText + "%");
             */

            // todo

            Predicate studentSurnamePredicate = qb.like(root.get("student").get("surname"), "%" + text + "%");

            Predicate subjectNamePredicate = qb.like(root.get("subject").get("name"), "%" + text + "%");
            Predicate subjectAbbreviationPredicate = qb.like(root.get("subject").get("abbreviation"), "%" + text + "%");

            Predicate classroomLabelPredicate = qb.like(root.get("classroom").get("label"), "%" + text + "%");

            Predicate chairmanSurnamePredicate = qb.like(root.get("chairman").get("surname"), "%" + text + "%");
            Predicate class_teacherSurnamePredicate = qb.like(root.get("class_teacher").get("surname"), "%" + text + "%");
            Predicate examinerSurnamePredicate = qb.like(root.get("examiner").get("surname"), "%" + text + "%");

            Predicate timePredicate = qb.like(root.get("time"), "%" + text + "%");

            Predicate searchPredicate = qb.or(studentSurnamePredicate, subjectNamePredicate, subjectAbbreviationPredicate, classroomLabelPredicate, chairmanSurnamePredicate, class_teacherSurnamePredicate, examinerSurnamePredicate, timePredicate);
            query.where(searchPredicate);

        }

        return em.createQuery(query).getResultList();
    }

    public enum FILTER_STATUS {
        FINISHED,
        UNFINISHED
    }

    public enum FILTER_TYPE {
        NAHRADNI_HODNOCENI,
        OPRAVNA_ZKOUSKA
    }

    public enum FILTER_SUCCESS {
        SUCCESSFUL,
        FAILED
    }

    public enum SORT_BY {
        STUDENT,
        TEACHER,
        CLASS,
        MARK
    }
}
