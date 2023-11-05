package cz.spse.nahradnihodnoceni.models.data;

import jakarta.persistence.*;
import lombok.*;

@Entity
@AllArgsConstructor
@NoArgsConstructor
@Builder
@Getter
@Setter
public class TeacherSuitability {
    @EmbeddedId
    TeacherSuitabilityKey id;

    @ManyToOne
    @MapsId("teacherId")
    @JoinColumn(name = "teacher_id")
    private Teacher teacher;

    @ManyToOne
    @MapsId("subjectId")
    @JoinColumn(name = "subject_id")
    private Subject subject;

    @Column(nullable = false)
    @Builder.Default
    private int suitability = 0;
}
