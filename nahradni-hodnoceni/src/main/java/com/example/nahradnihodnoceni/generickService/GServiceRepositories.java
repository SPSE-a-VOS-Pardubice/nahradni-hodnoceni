package com.example.nahradnihodnoceni.generickService;

import java.util.HashMap;
import java.util.Map;
import java.util.NoSuchElementException;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.data.mongodb.repository.MongoRepository;
import org.springframework.stereotype.Service;

import com.example.nahradnihodnoceni.models.Classroom;
import com.example.nahradnihodnoceni.models.Exam;
import com.example.nahradnihodnoceni.models.Student;
import com.example.nahradnihodnoceni.models.Subject;
import com.example.nahradnihodnoceni.models.Teacher;
import com.example.nahradnihodnoceni.models.Trait;
import com.example.nahradnihodnoceni.models._Class;
import com.example.nahradnihodnoceni.repositories.ClassroomRepository;
import com.example.nahradnihodnoceni.repositories.ExamRepository;
import com.example.nahradnihodnoceni.repositories.StudentRepository;
import com.example.nahradnihodnoceni.repositories.SubjectRepository;
import com.example.nahradnihodnoceni.repositories.TeacherRepository;
import com.example.nahradnihodnoceni.repositories.TraitRepository;
import com.example.nahradnihodnoceni.repositories._ClassRepository;
import com.example.nahradnihodnoceni.generickService.DuplicateKeyException;
import com.example.nahradnihodnoceni.generickService.ValidationException;

@Service
public class GServiceRepositories {

    private Map<String, MongoRepository> repositories = null;
    private Map<String, Validator> validations = new HashMap<>();

    @Autowired
    private _ClassRepository classRepository;
    @Autowired
    private ClassroomRepository classroomRepository;
    @Autowired
    private ExamRepository examRepository;
    @Autowired
    private StudentRepository studentRepository;
    @Autowired
    private SubjectRepository subjectRepository;
    @Autowired
    private TeacherRepository teacherRepository;
    @Autowired
    private TraitRepository traitRepository;

    public GServiceRepositories() {
        super();
        setValidations();
    }

    public void setRepositories() {
        repositories = new HashMap<>();
        repositories.put(_Class.class.getName(), classRepository);
        repositories.put(Classroom.class.getName(), classroomRepository);
        repositories.put(Exam.class.getName(), examRepository);
        repositories.put(Student.class.getName(), studentRepository);
        repositories.put(Subject.class.getName(), subjectRepository);
        repositories.put(Teacher.class.getName(), teacherRepository);
        repositories.put(Trait.class.getName(), traitRepository);
    }

    public void setValidations() {

        validations.put(_Class.class.getName(), d -> {
            // TODO Auto-generated method
            return;
        });
        validations.put(Classroom.class.getName(), d -> {
            // TODO Auto-generated method
            return;
        });
        validations.put(Exam.class.getName(), d -> {
            // TODO Auto-generated method
            return;
        });
        validations.put(Student.class.getName(), d -> {
            // TODO Auto-generated method
            return;
        });
        validations.put(Subject.class.getName(), d -> {
            // TODO Auto-generated method
            return;
        });
        validations.put(Teacher.class.getName(), d -> {
            // TODO Auto-generated method
            return;
        });
        validations.put(Trait.class.getName(), d -> {
            // TODO Auto-generated method
            return;
        });
    }

    public <E> MongoRepository<E, Object> getRepository(E data) throws NoSuchElementException {

        if (repositories == null) {
            setRepositories();
        }

        MongoRepository repository = repositories.get(data.getClass().getName());

        if (repository == null) {
            throw new NoSuchElementException();
        }

        return (MongoRepository<E, Object>) repository;
    }

    public <E> void validate(E data) throws NoSuchElementException, ValidationException, DuplicateKeyException {
        Validator<E> validate = validations.get(data.getClass().getName());

        validate.validate(data);
    }
}
