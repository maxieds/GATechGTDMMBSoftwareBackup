# RNADB Minimal Sequence Entry Description

The following is a listing of the minimal sequence metadata needed to generate RNADB entries using the construction script. Each of these items should be created to append a new sequence in this repository for processing to display on RNADB:

We require essentially (minimally) three files to specify a sequence to the [RNADB-construction script](https://github.gatech.edu/mschmidt34/RNADB-construction):
1. **sequence-name.ct** : Standard CT file data for the sequence;
2. **sequence-name.nopct** : Standardized CT file data with additional header information and removed pseudoknot data (**Optional file**);
3. **sequence-name.paper** : A text file containing a *comma separated* (no spaces) list of paper keys in the RNADB database which are associated with this sequence;
4. **sequence-name.comments** (optional): A plaintext file containing comments on the sequence (**Optional file**). 

As an example, here are the files associated with an entry for *Homo sapiens* (tRNA):

**AC004932_g.ct:**
```
>Filename: AC004932_g.ct
>Organism: Homo sapiens
>Accession Numbers: AC004932
>Citation and related information available at http://www.rna.ccbb.utexas.edu
72   dG =     0.00  [initially     0.0]
 1  G   0   2  71   1
 2  G   1   3  70   2
 3  G   2   4  69   3
 4  C   3   5  68   4
 5  G   4   6  67   5
 6  U   5   7  66   6
 7  A   6   8  65   7
 8  U   7   9   0   8
 9  A   8  10   0   9
10  G   9  11  24  10
11  C  10  12  23  11
12  U  11  13  22  12
13  C  12  14  21  13
14  A  13  15   0  14
15  G  14  16   0  15
16  G  15  17   0  16
17  G  16  18   0  17
18  G  17  19   0  18
19  U  18  20   0  19
20  A  19  21   0  20
21  G  20  22  13  21
22  A  21  23  12  22
23  G  22  24  11  23
24  C  23  25  10  24
25  A  24  26  43  25
26  U  25  27  42  26
27  U  26  28  41  27
28  U  27  29  40  28
29  G  28  30  39  29
30  A  29  31  38  30
31  C  30  32   0  31
32  U  31  33   0  32
33  G  32  34   0  33
34  C  33  35   0  34
35  A  34  36   0  35
36  G  35  37   0  36
37  A  36  38   0  37
38  U  37  39  30  38
39  C  38  40  29  39
40  A  39  41  28  40
41  A  40  42  27  41
42  G  41  43  26  42
43  A  42  44  25  43
44  G  43  45   0  44
45  G  44  46   0  45
46  U  45  47   0  46
47  C  46  48   0  47
48  C  47  49  64  48
49  C  48  50  63  49
50  C  49  51  62  50
51  A  50  52  61  51
52  G  51  53  60  52
53  U  52  54   0  53
54  U  53  55   0  54
55  C  54  56   0  55
56  A  55  57   0  56
57  A  56  58   0  57
58  A  57  59   0  58
59  U  58  60   0  59
60  C  59  61  52  60
61  U  60  62  51  61
62  G  61  63  50  62
63  G  62  64  49  63
64  G  63  65  48  64
65  U  64  66   7  65
66  G  65  67   6  66
67  C  66  68   5  67
68  C  67  69   4  68
69  C  68  70   3  69
70  C  69  71   2  70
71  C  70  72   1  71
72  U  71  73   0  72
```

**AC004932_g.nopct:**
```
Filename: AC004932_g.nopct
Organism: Homo sapiens
Accession Numbers: AC004932
Citation and related information available at http://www.rna.ccbb.utexas.edu
72   dG =     0.00  [initially     0.0]
 1  G   0   2  71   1
 2  G   1   3  70   2
 3  G   2   4  69   3
 4  C   3   5  68   4
 5  G   4   6  67   5
 6  U   5   7  66   6
 7  A   6   8  65   7
 8  U   7   9   0   8
 9  A   8  10   0   9
10  G   9  11  24  10
11  C  10  12  23  11
12  U  11  13  22  12
13  C  12  14  21  13
14  A  13  15   0  14
15  G  14  16   0  15
16  G  15  17   0  16
17  G  16  18   0  17
18  G  17  19   0  18
19  U  18  20   0  19
20  A  19  21   0  20
21  G  20  22  13  21
22  A  21  23  12  22
23  G  22  24  11  23
24  C  23  25  10  24
25  A  24  26  43  25
26  U  25  27  42  26
27  U  26  28  41  27
28  U  27  29  40  28
29  G  28  30  39  29
30  A  29  31  38  30
31  C  30  32   0  31
32  U  31  33   0  32
33  G  32  34   0  33
34  C  33  35   0  34
35  A  34  36   0  35
36  G  35  37   0  36
37  A  36  38   0  37
38  U  37  39  30  38
39  C  38  40  29  39
40  A  39  41  28  40
41  A  40  42  27  41
42  G  41  43  26  42
43  A  42  44  25  43
44  G  43  45   0  44
45  G  44  46   0  45
46  U  45  47   0  46
47  C  46  48   0  47
48  C  47  49  64  48
49  C  48  50  63  49
50  C  49  51  62  50
51  A  50  52  61  51
52  G  51  53  60  52
53  U  52  54   0  53
54  U  53  55   0  54
55  C  54  56   0  55
56  A  55  57   0  56
57  A  56  58   0  57
58  A  57  59   0  58
59  U  58  60   0  59
60  C  59  61  52  60
61  U  60  62  51  61
62  G  61  63  50  62
63  G  62  64  49  63
64  G  63  65  48  64
65  U  64  66   7  65
66  G  65  67   6  66
67  C  66  68   5  67
68  C  67  69   4  68
69  C  68  70   3  69
70  C  69  71   2  70
71  C  70  72   1  71
72  U  71  73   0  72
```

**d.16.m.C.elegans.paper:**
```
rogers14
```