(define list-n 
  (lambda (n) 
    (if (zero? n) '() 
	(cons n (list-n (- n 1))))))

(define list-ref
  (lambda (l n)
    (if (zero? n)
	(car l)
	(list-ref (cdr l) (- n 1)))))

(define filter 
  (lambda (l f) 
    (if (null? l) '() 
	(if (f (car l)) 
	    (cons (car l) (filter (cdr l) f)) 
	    (filter (cdr l) f)))))

(define reverse
  (letrec
      ((rev
	(lambda (l acc)
	  (if (null? l) acc
	      (rev (cdr l) (cons (car l) acc))))))
    (lambda (l)
      (rev l '()))))

(define append
  (lambda (l . ls)
    (if (null? l)
	(if (pair? ls)
	    (if (pair? (cdr ls))
		(apply append ls)
		(car ls)) ls)
	(cons (car l)
	      (apply append (cons (cdr l) ls))))))

(define equal?
  (lambda (obj1 obj2)
    (if (and (pair? obj1) (pair? obj2))
	(and (equal? (car obj1) (car obj2))
	     (equal? (cdr obj1) (cdr obj2)))
	(if (or (pair? obj1) (pair? obj2)) #f
	    (eqv? obj1 obj2)))))

(define memgeneric
  (lambda (obj l pred)
    (if (null? l) '()
	(if (pred obj (car l)) l
	    (memgeneric obj (cdr l) pred)))))

(define memq
  (lambda (obj l) (memgeneric obj l eq?)))
(define memv
  (lambda (obj l) (memgeneric obj l eqv?)))
(define member
  (lambda (obj l) (memgeneric obj l equal?)))

(define association
  (lambda (obj l pred)
    (if (null? l) #f
	(if (and (pair? (car l))
		 (pred obj (car (car l))))
	    (car l)
	    (association obj (cdr l) pred)))))

(define assq
  (lambda (obj l) (association obj l eq?)))
(define assv
  (lambda (obj l) (association obj l eqv?)))
(define assoc
  (lambda (obj l) (association obj l equal?)))


(define map-over-single-list
  (lambda (p l)
    (if (null? l) '()
	(cons (p (car l)) 
	      (map-over-single-list p (cdr l))))))

(define map
  (lambda (proc . lists)
    (if (memq '() lists) '()
	(cons
	 (apply proc 
		(map-over-single-list car lists))
	 (apply map 
		(cons proc (map-over-single-list cdr lists)))))))

(define for-each
  (lambda (proc . lists)
    (if (memq '() lists) '()
	(begin
	 (apply proc 
		(map-over-single-list car lists))
	 (apply for-each 
		(cons proc (map-over-single-list cdr lists)))))))
