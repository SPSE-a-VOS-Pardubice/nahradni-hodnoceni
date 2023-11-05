package cz.spse.nahradnihodnoceni.models.data;

import jakarta.persistence.*;
import lombok.*;

import java.io.Serializable;

@Embeddable
@AllArgsConstructor
@NoArgsConstructor
@Data
public class TeacherSuitabilityKey implements Serializable {
    @Column(name = "teacher_id")
    private int teacherId;

    @Column(name = "subject_id")
    private int subjectId;
}
